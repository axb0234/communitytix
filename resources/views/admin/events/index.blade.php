@extends('admin.layout')
@section('page-title', 'Events')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form class="d-flex flex-wrap gap-2 filter-bar" method="GET">
        <input type="text" name="search" class="form-control form-control-sm" style="min-width:120px;max-width:200px;" placeholder="Search..." value="{{ request('search') }}">
        <select name="status" class="form-select form-select-sm" style="width:120px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <select name="type" class="form-select form-select-sm" style="width:120px;" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="FREE" {{ request('type') === 'FREE' ? 'selected' : '' }}>Free</option>
            <option value="TICKETED" {{ request('type') === 'TICKETED' ? 'selected' : '' }}>Ticketed</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    </form>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.events.export', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i>Export</a>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>New Event</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0 table-responsive-wrap">
        <table class="table table-hover mb-0 align-middle" style="white-space:nowrap;">
            <thead>
                <tr class="table-light">
                    <th rowspan="2" class="align-bottom">Event</th>
                    <th rowspan="2" class="align-bottom">Date</th>
                    <th rowspan="2" class="align-bottom text-center">Type</th>
                    <th rowspan="2" class="align-bottom text-center">Status</th>
                    <th colspan="3" class="text-center border-start small text-uppercase text-muted">Guests</th>
                    <th colspan="4" class="text-center border-start small text-uppercase text-muted">Tickets Sold</th>
                    <th colspan="4" class="text-center border-start small text-uppercase text-muted">Revenue ({{ $currency }})</th>
                    <th rowspan="2" class="align-bottom border-start"></th>
                </tr>
                <tr class="table-light">
                    <th class="text-center border-start small">RSVP</th>
                    <th class="text-center small">Ticketed</th>
                    <th class="text-center small fw-bold">Total</th>
                    <th class="text-center border-start small">Online</th>
                    <th class="text-center small">Cash</th>
                    <th class="text-center small">Card</th>
                    <th class="text-center small fw-bold">Total</th>
                    <th class="text-center border-start small">Online</th>
                    <th class="text-center small">Cash</th>
                    <th class="text-center small">Card</th>
                    <th class="text-center small fw-bold">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                @php
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
                @endphp
                <tr>
                    <td><a href="{{ route('admin.events.edit', $event) }}">{{ Str::limit($event->title, 35) }}</a></td>
                    <td>{{ $event->start_at->format('M j, Y') }}</td>
                    <td class="text-center"><span class="badge bg-{{ $event->event_type === 'TICKETED' ? 'warning text-dark' : 'success' }}">{{ $event->event_type }}</span></td>
                    <td class="text-center"><span class="badge bg-{{ $event->status === 'published' ? 'success' : 'secondary' }}">{{ $event->status }}</span></td>
                    <td class="text-center border-start">{{ $rsvpGuests }}</td>
                    <td class="text-center">{{ $ticketedGuests }}</td>
                    <td class="text-center fw-bold">{{ $totalGuests }}</td>
                    <td class="text-center border-start">{{ $onlineTickets }}</td>
                    <td class="text-center">{{ $cashSales }}</td>
                    <td class="text-center">{{ $cardSales }}</td>
                    <td class="text-center fw-bold">{{ $ticketedGuests }}</td>
                    <td class="text-center border-start">{{ number_format($onlineRev, 2) }}</td>
                    <td class="text-center">{{ number_format($cashRev, 2) }}</td>
                    <td class="text-center">{{ number_format($cardRev, 2) }}</td>
                    <td class="text-center fw-bold">{{ number_format($totalRev, 2) }}</td>
                    <td class="border-start">
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="d-inline" onsubmit="return confirm('Delete this event?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="16" class="text-center text-muted py-3">No events yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $events->links() }}</div>
@endsection
