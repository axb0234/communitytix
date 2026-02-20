<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\OrderItem;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    private function buildEventQuery(Request $request)
    {
        $query = Event::orderByDesc('start_at')
            ->withSum(['orders as online_revenue' => fn($q) => $q->where('status', 'COMPLETED')], 'total_amount')
            ->withSum('cashCollections as cash_revenue', 'amount')
            ->withSum('posPayments as card_revenue', 'amount')
            ->withSum('rsvps as rsvp_guests', 'guests')
            ->withCount([
                'cashCollections as cash_sales_count',
                'posPayments as card_sales_count',
            ])
            ->addSelect([
                'online_tickets_sold' => OrderItem::selectRaw('COALESCE(SUM(order_items.qty), 0)')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereColumn('orders.event_id', 'events.id')
                    ->where('orders.status', 'COMPLETED'),
            ]);

        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('event_type', $request->type);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $currency = app('current_tenant')->currency;
        $events = $this->buildEventQuery($request)->paginate(15)->withQueryString();
        return view('admin.events.index', compact('events', 'currency'));
    }

    public function export(Request $request): StreamedResponse
    {
        $currency = app('current_tenant')->currency;
        $events = $this->buildEventQuery($request)->get();
        $tenant = app('current_tenant');
        $filename = Str::slug($tenant->name) . '-events-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($events, $currency) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Event', 'Date', 'Type', 'Status',
                'Guests RSVP', 'Guests Ticketed', 'Guests Total',
                'Tickets Online', 'Tickets Cash', 'Tickets Card', 'Tickets Total',
                "Revenue Online ($currency)", "Revenue Cash ($currency)", "Revenue Card ($currency)", "Revenue Total ($currency)",
            ]);

            foreach ($events as $event) {
                $rsvpGuests = (int) ($event->rsvp_guests ?? 0);
                $onlineTickets = (int) $event->online_tickets_sold;
                $cashSales = (int) $event->cash_sales_count;
                $cardSales = (int) $event->card_sales_count;
                $ticketedGuests = $onlineTickets + $cashSales + $cardSales;
                $totalGuests = $rsvpGuests + $ticketedGuests;

                $onlineRev = (float) ($event->online_revenue ?? 0);
                $cashRev = (float) ($event->cash_revenue ?? 0);
                $cardRev = (float) ($event->card_revenue ?? 0);
                $totalRev = $onlineRev + $cashRev + $cardRev;

                fputcsv($handle, [
                    $event->title,
                    $event->start_at->format('Y-m-d'),
                    $event->event_type,
                    $event->status,
                    $rsvpGuests,
                    $ticketedGuests,
                    $totalGuests,
                    $onlineTickets,
                    $cashSales,
                    $cardSales,
                    $ticketedGuests,
                    number_format($onlineRev, 2, '.', ''),
                    number_format($cashRev, 2, '.', ''),
                    number_format($cardRev, 2, '.', ''),
                    number_format($totalRev, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create()
    {
        return view('admin.events.form', ['event' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'location' => 'nullable|string|max:255',
            'location_address' => 'nullable|string|max:500',
            'event_type' => 'required|in:FREE,TICKETED',
            'status' => 'required|in:draft,published',
            'body_html' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'rsvp_capacity' => 'nullable|integer|min:1',
            'flyer_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'pwyw_enabled' => 'nullable|boolean',
            'pwyw_amount_1' => 'nullable|numeric|min:0.01',
            'pwyw_amount_2' => 'nullable|numeric|min:0.01',
            'pwyw_amount_3' => 'nullable|numeric|min:0.01',
        ]);

        $data['pwyw_enabled'] = $request->boolean('pwyw_enabled', false);
        $data['slug'] = Str::slug($data['title']);
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('flyer_file')) {
            $data['flyer_path'] = $this->storeUpload($request->file('flyer_file'), 'events/flyers');
        }
        unset($data['flyer_file']);

        $baseSlug = $data['slug'];
        $counter = 1;
        while (Event::withoutGlobalScopes()->where('tenant_id', app('current_tenant')->id)->where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        $event = Event::create($data);
        AuditLog::log('event_created', 'Event', $event->id);

        return redirect()->route('admin.events.edit', $event)->with('success', 'Event created. Now add ticket types or images.');
    }

    public function edit(Event $event)
    {
        $event->load('ticketTypes', 'images');
        return view('admin.events.form', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'location' => 'nullable|string|max:255',
            'location_address' => 'nullable|string|max:500',
            'event_type' => 'required|in:FREE,TICKETED',
            'status' => 'required|in:draft,published',
            'body_html' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'rsvp_capacity' => 'nullable|integer|min:1',
            'flyer_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'pwyw_enabled' => 'nullable|boolean',
            'pwyw_amount_1' => 'nullable|numeric|min:0.01',
            'pwyw_amount_2' => 'nullable|numeric|min:0.01',
            'pwyw_amount_3' => 'nullable|numeric|min:0.01',
        ]);

        $data['pwyw_enabled'] = $request->boolean('pwyw_enabled', false);

        if ($data['status'] === 'published' && !$event->published_at) {
            $data['published_at'] = now();
            AuditLog::log('event_published', 'Event', $event->id);
        }

        if ($request->hasFile('flyer_file')) {
            $data['flyer_path'] = $this->storeUpload($request->file('flyer_file'), 'events/flyers');
        }
        unset($data['flyer_file']);

        $event->update($data);
        return redirect()->route('admin.events.edit', $event)->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }

    // Ticket Types
    public function storeTicketType(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'sort_order' => 'integer',
        ]);
        $data['event_id'] = $event->id;

        TicketType::create($data);
        return redirect()->route('admin.events.edit', $event)->with('success', 'Ticket type added.');
    }

    public function updateTicketType(Request $request, Event $event, TicketType $ticketType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        $ticketType->update($data);
        return redirect()->route('admin.events.edit', $event)->with('success', 'Ticket type updated.');
    }

    public function destroyTicketType(Event $event, TicketType $ticketType)
    {
        $ticketType->delete();
        return redirect()->route('admin.events.edit', $event)->with('success', 'Ticket type deleted.');
    }

    // Event Images
    public function storeImage(Request $request, Event $event)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $this->storeUpload($request->file('image'), 'events/images');

        EventImage::create([
            'event_id' => $event->id,
            'image_path' => $path,
            'caption' => $request->caption,
            'sort_order' => $event->images()->count(),
        ]);

        return redirect()->route('admin.events.edit', $event)->with('success', 'Image added.');
    }

    public function destroyImage(Event $event, EventImage $image)
    {
        $image->delete();
        return redirect()->route('admin.events.edit', $event)->with('success', 'Image deleted.');
    }

    private function storeUpload($file, string $folder): string
    {
        $tenant = app('current_tenant');
        $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = "uploads/{$tenant->slug}/{$folder}";
        $file->move(storage_path("app/public/{$path}"), $name);
        return "{$path}/{$name}";
    }
}
