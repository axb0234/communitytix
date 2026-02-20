<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Event;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayPalSetting;
use App\Models\Rsvp;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::published()
            ->upcoming()
            ->orderBy('start_at')
            ->paginate(12);

        $pastEvents = Event::published()
            ->where('start_at', '<', now())
            ->orderByDesc('start_at')
            ->limit(6)
            ->get();

        return view('public.events.index', compact('upcomingEvents', 'pastEvents'));
    }

    public function show(string $slug)
    {
        $event = Event::published()
            ->where('slug', $slug)
            ->with(['images', 'ticketTypes' => fn($q) => $q->where('active', true)])
            ->firstOrFail();

        return view('public.events.show', compact('event'));
    }

    public function rsvp(Request $request, string $slug)
    {
        $event = Event::published()->where('slug', $slug)->where('event_type', 'FREE')->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'guests' => 'required|integer|min:1|max:10',
        ]);

        // Check capacity
        if ($event->rsvp_capacity) {
            $currentCount = $event->rsvps()->sum('guests');
            if ($currentCount + $data['guests'] > $event->rsvp_capacity) {
                return back()->with('error', 'Sorry, this event has reached capacity.')->withInput();
            }
        }

        // Check for duplicate
        $existing = Rsvp::withoutGlobalScopes()
            ->where('tenant_id', app('current_tenant')->id)
            ->where('event_id', $event->id)
            ->where('email', $data['email'])
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already RSVP\'d for this event.')->withInput();
        }

        $data['event_id'] = $event->id;
        Rsvp::create($data);

        // Create guest member if not exists
        $this->ensureGuestMember($data['name'], $data['email'], $data['phone'] ?? null);

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Your RSVP has been confirmed! See you there.');
    }

    public function checkout(Request $request, string $slug)
    {
        $event = Event::published()
            ->where('slug', $slug)
            ->where('event_type', 'TICKETED')
            ->with(['ticketTypes' => fn($q) => $q->where('active', true)])
            ->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tickets' => 'nullable|array',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.qty' => 'required|integer|min:0|max:20',
            'pwyw_amount' => 'nullable|numeric|min:0.01',
        ]);

        $tickets = collect($data['tickets'] ?? [])->filter(fn($t) => $t['qty'] > 0);
        $pwywAmount = $data['pwyw_amount'] ?? 0;

        if ($tickets->isEmpty() && !($event->isPwyw() && $pwywAmount > 0)) {
            return back()->with('error', 'Please select at least one ticket or enter a Pay What You Can amount.')->withInput();
        }

        // Validate availability and calculate total
        $totalAmount = 0;
        $itemsForPaypal = [];
        $orderItemsData = [];

        foreach ($tickets as $ticketData) {
            $ticketType = $event->ticketTypes->find($ticketData['ticket_type_id']);
            if (!$ticketType) continue;

            if ($ticketType->capacity !== null && $ticketType->available < $ticketData['qty']) {
                return back()->with('error', "Not enough '{$ticketType->name}' tickets available.")->withInput();
            }

            $lineTotal = $ticketType->price * $ticketData['qty'];
            $totalAmount += $lineTotal;

            $itemsForPaypal[] = [
                'name' => $ticketType->name,
                'quantity' => $ticketData['qty'],
                'unit_price' => $ticketType->price,
            ];

            $orderItemsData[] = [
                'ticket_type_id' => $ticketType->id,
                'qty' => $ticketData['qty'],
                'unit_price' => $ticketType->price,
            ];
        }

        // Add PWYC item if applicable
        if ($event->isPwyw() && $pwywAmount > 0) {
            $totalAmount += $pwywAmount;

            $itemsForPaypal[] = [
                'name' => 'Pay What You Can',
                'quantity' => 1,
                'unit_price' => $pwywAmount,
            ];

            $orderItemsData[] = [
                'ticket_type_id' => null,
                'qty' => 1,
                'unit_price' => $pwywAmount,
            ];
        }

        $tenant = app('current_tenant');
        $paypalSetting = PayPalSetting::where('tenant_id', $tenant->id)->first();

        if (!$paypalSetting) {
            return back()->with('error', 'Payment is not configured for this organisation. Please contact the organiser.');
        }

        // Create order
        $order = Order::create([
            'event_id' => $event->id,
            'order_number' => Order::generateOrderNumber(),
            'purchaser_name' => $data['name'],
            'purchaser_email' => $data['email'],
            'purchaser_phone' => $data['phone'] ?? null,
            'status' => 'PENDING',
            'total_amount' => $totalAmount,
            'currency' => $tenant->currency,
            'payment_method' => 'PAYPAL',
        ]);

        foreach ($orderItemsData as $item) {
            $order->items()->create($item);
        }

        // Create PayPal order
        try {
            $paypalService = new PayPalService($paypalSetting);
            $paypalOrder = $paypalService->createOrder(
                $itemsForPaypal,
                $tenant->currency,
                route('events.checkout.success', ['slug' => $event->slug, 'order' => $order->id]),
                route('events.checkout.cancel', ['slug' => $event->slug, 'order' => $order->id])
            );

            $order->update(['provider_order_id' => $paypalOrder['id']]);

            // Find approval URL (PayPal returns 'payer-action' with payment_source, 'approve' with application_context)
            $approvalUrl = collect($paypalOrder['links'])->firstWhere('rel', 'payer-action')['href']
                ?? collect($paypalOrder['links'])->firstWhere('rel', 'approve')['href']
                ?? null;

            if (!$approvalUrl) {
                throw new \RuntimeException('No approval URL from PayPal');
            }

            return redirect($approvalUrl);
        } catch (\Exception $e) {
            $order->update(['status' => 'FAILED']);
            return back()->with('error', 'Payment processing failed. Please try again.')->withInput();
        }
    }

    public function checkoutSuccess(Request $request, string $slug, Order $order)
    {
        $event = Event::published()->where('slug', $slug)->firstOrFail();
        $tenant = app('current_tenant');

        if ($order->status !== 'PENDING') {
            return redirect()->route('events.show', $slug)->with('info', 'This order has already been processed.');
        }

        $paypalSetting = PayPalSetting::where('tenant_id', $tenant->id)->firstOrFail();

        try {
            $paypalService = new PayPalService($paypalSetting);
            $capture = $paypalService->captureOrder($order->provider_order_id);

            if (($capture['status'] ?? '') === 'COMPLETED') {
                $captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
                $order->update([
                    'status' => 'COMPLETED',
                    'provider_capture_id' => $captureId,
                    'paid_at' => now(),
                ]);

                $this->ensureGuestMember($order->purchaser_name, $order->purchaser_email, $order->purchaser_phone);

                // Send confirmation email
                try {
                    $order->load('items.ticketType', 'event');
                    Mail::to($order->purchaser_email)->send(new OrderConfirmation($order, $tenant));
                } catch (\Exception $mailEx) {
                    Log::error('Failed to send order confirmation email', ['order' => $order->order_number, 'error' => $mailEx->getMessage()]);
                }

                return view('public.events.checkout-success', compact('order', 'event'));
            }
        } catch (\Exception $e) {
            Log::error('Checkout capture failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            // Don't fail - webhook will handle capture
        }

        // Refresh order in case webhook already captured it
        $order->refresh();
        return view('public.events.checkout-success', compact('order', 'event'));
    }

    public function checkoutCancel(string $slug, Order $order)
    {
        $order->update(['status' => 'CANCELLED']);
        return redirect()->route('events.show', $slug)->with('info', 'Payment was cancelled.');
    }

    private function ensureGuestMember(string $name, string $email, ?string $phone): void
    {
        $tenant = app('current_tenant');
        $parts = explode(' ', $name, 2);

        Member::firstOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $email],
            [
                'member_type' => 'GUEST',
                'status' => 'ACTIVE',
                'first_name' => $parts[0],
                'last_name' => $parts[1] ?? '',
                'phone' => $phone,
            ]
        );
    }
}
