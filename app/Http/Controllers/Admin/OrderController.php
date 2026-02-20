<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CashCollection;
use App\Models\Event;
use App\Models\Order;
use App\Models\PosPayment;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    private function buildOrderQuery(Request $request)
    {
        $query = Order::with(['event'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('purchaser_email', 'ilike', '%' . $request->search . '%')
                  ->orWhere('purchaser_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('order_number', 'ilike', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $orders = $this->buildOrderQuery($request)->paginate(20)->withQueryString();
        $events = Event::orderByDesc('start_at')->get();
        return view('admin.orders.index', compact('orders', 'events'));
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = $this->buildOrderQuery($request)->get();
        $tenant = app('current_tenant');
        $filename = Str::slug($tenant->name) . '-orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders, $tenant) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Order #', 'Purchaser Name', 'Email', 'Phone',
                'Event', 'Amount (' . $tenant->currency . ')', 'Payment Method',
                'Status', 'Order Date', 'Paid At',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->purchaser_name,
                    $order->purchaser_email,
                    $order->purchaser_phone ?? '',
                    $order->event->title ?? '-',
                    number_format($order->total_amount, 2, '.', ''),
                    $order->payment_method,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->paid_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items.ticketType', 'event');
        return view('admin.orders.show', compact('order'));
    }

    public function markRefunded(Order $order)
    {
        $order->update([
            'status' => 'REFUNDED',
            'refunded_at' => now(),
            'refunded_by' => auth()->id(),
        ]);

        AuditLog::log('order_refunded', 'Order', $order->id, [
            'order_number' => $order->order_number,
            'amount' => $order->total_amount,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order marked as refunded.');
    }

    // RSVPs
    private function buildRsvpQuery(Request $request)
    {
        $query = Rsvp::with('event')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('email', 'ilike', '%' . $request->search . '%');
            });
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function rsvps(Request $request)
    {
        $query = $this->buildRsvpQuery($request);
        $totalGuests = (clone $query)->sum('guests');
        $rsvps = $query->paginate(20)->withQueryString();
        $events = Event::where('event_type', 'FREE')->orderByDesc('start_at')->get();
        return view('admin.orders.rsvps', compact('rsvps', 'events', 'totalGuests'));
    }

    public function exportRsvps(Request $request): StreamedResponse
    {
        $rsvps = $this->buildRsvpQuery($request)->get();
        $tenant = app('current_tenant');
        $filename = Str::slug($tenant->name) . '-rsvps-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rsvps) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Name', 'Email', 'Phone', 'Guests', 'Event', 'RSVP Date']);

            foreach ($rsvps as $rsvp) {
                fputcsv($handle, [
                    $rsvp->name,
                    $rsvp->email,
                    $rsvp->phone ?? '',
                    $rsvp->guests,
                    $rsvp->event->title ?? '-',
                    $rsvp->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // Cash Collections
    private function buildCashQuery(Request $request)
    {
        $query = CashCollection::with(['event', 'collectedBy'])->orderByDesc('collected_at');

        if ($request->filled('search')) {
            $query->whereHas('event', fn($q) => $q->where('title', 'ilike', '%' . $request->search . '%'));
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('collected_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('collected_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function cashIndex(Request $request)
    {
        $query = $this->buildCashQuery($request);
        $totalAmount = (clone $query)->sum('amount');
        $collections = $query->paginate(20)->withQueryString();
        $events = Event::orderByDesc('start_at')->get();
        return view('admin.orders.cash', compact('collections', 'events', 'totalAmount'));
    }

    public function exportCash(Request $request): StreamedResponse
    {
        $collections = $this->buildCashQuery($request)->get();
        $tenant = app('current_tenant');
        $filename = Str::slug($tenant->name) . '-cash-collections-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($collections, $tenant) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Event', 'Amount (' . $tenant->currency . ')', 'Collected By', 'Date', 'Notes']);

            foreach ($collections as $c) {
                fputcsv($handle, [
                    $c->event->title ?? '-',
                    number_format($c->amount, 2, '.', ''),
                    $c->collectedBy->name ?? '-',
                    $c->collected_at->format('Y-m-d H:i'),
                    $c->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function storeCash(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['collected_by_user_id'] = auth()->id();
        $data['collected_at'] = now();

        CashCollection::create($data);
        AuditLog::log('cash_collection_recorded', 'CashCollection', null, $data);

        return redirect()->route('admin.orders.cash')->with('success', 'Cash collection recorded.');
    }

    // POS Payments (card at door)
    private function buildPosQuery(Request $request)
    {
        $query = PosPayment::with(['event', 'recordedBy'])->orderByDesc('recorded_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'ilike', '%' . $request->search . '%')
                  ->orWhereHas('event', fn($eq) => $eq->where('title', 'ilike', '%' . $request->search . '%'));
            });
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('recorded_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('recorded_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function posIndex(Request $request)
    {
        $query = $this->buildPosQuery($request);
        $totalAmount = (clone $query)->sum('amount');
        $payments = $query->paginate(20)->withQueryString();
        $events = Event::orderByDesc('start_at')->get();
        return view('admin.orders.pos', compact('payments', 'events', 'totalAmount'));
    }

    public function exportPos(Request $request): StreamedResponse
    {
        $payments = $this->buildPosQuery($request)->get();
        $tenant = app('current_tenant');
        $filename = Str::slug($tenant->name) . '-pos-payments-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($payments, $tenant) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Event', 'Amount (' . $tenant->currency . ')', 'Reference', 'Status', 'Recorded By', 'Date', 'Notes']);

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->event->title ?? '-',
                    number_format($p->amount, 2, '.', ''),
                    $p->reference ?? '',
                    $p->status,
                    $p->recordedBy->name ?? '-',
                    $p->recorded_at->format('Y-m-d H:i'),
                    $p->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function storePos(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['method'] = 'CARD';
        $data['status'] = 'PENDING_RECONCILIATION';
        $data['recorded_by_user_id'] = auth()->id();
        $data['recorded_at'] = now();

        PosPayment::create($data);
        AuditLog::log('pos_payment_recorded', 'PosPayment', null, $data);

        return redirect()->route('admin.orders.pos')->with('success', 'Card payment recorded for reconciliation.');
    }
}
