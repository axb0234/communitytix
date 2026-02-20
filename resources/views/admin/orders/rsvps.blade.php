@extends('admin.layout')
@section('page-title', 'RSVPs')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <form method="GET">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <input type="text" name="search" class="form-control form-control-sm" style="min-width:140px;max-width:220px;" placeholder="Search name/email..." value="{{ request('search') }}">
            <select name="event_id" class="form-select form-select-sm" style="width:260px;" onchange="this.form.submit()">
                <option value="">All Events</option>
                @foreach($events as $ev)
                <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ Str::limit($ev->title, 28) }} ({{ $ev->start_at->format('j M Y') }})</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <input type="date" name="date_from" class="form-control form-control-sm" style="width:150px;" value="{{ request('date_from') }}" title="From date" onchange="this.form.submit()">
            <span class="text-muted small">to</span>
            <input type="date" name="date_to" class="form-control form-control-sm" style="width:150px;" value="{{ request('date_to') }}" title="To date" onchange="this.form.submit()">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <a href="{{ route('admin.orders.rsvps.export', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i>Export</a>
</div>

@if($totalGuests > 0)
<div class="alert alert-info py-2 mb-3">
    <i class="fas fa-users me-1"></i> <strong>{{ $totalGuests }}</strong> total guests across <strong>{{ $rsvps->total() }}</strong> RSVPs (matching current filters)
</div>
@endif

<div class="card">
    <div class="card-body p-0 table-responsive-wrap">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Guests</th><th>Event</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($rsvps as $rsvp)
                <tr>
                    <td>{{ $rsvp->name }}</td>
                    <td>{{ $rsvp->email }}</td>
                    <td>{{ $rsvp->phone ?? '-' }}</td>
                    <td>{{ $rsvp->guests }}</td>
                    <td>{{ Str::limit($rsvp->event->title ?? '-', 30) }}</td>
                    <td>{{ $rsvp->created_at->format('M j, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No RSVPs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $rsvps->links() }}</div>
@endsection
