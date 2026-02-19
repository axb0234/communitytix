@extends('admin.layout')
@section('page-title', 'RSVPs')

@section('content')
<form class="d-flex gap-2 mb-3" method="GET">
    <select name="event_id" class="form-select form-select-sm" style="width:250px;" onchange="this.form.submit()">
        <option value="">All Events</option>
        @foreach($events as $ev)
        <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->title }}</option>
        @endforeach
    </select>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Guests</th><th>Event</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($rsvps as $rsvp)
                <tr>
                    <td>{{ $rsvp->name }}</td>
                    <td>{{ $rsvp->email }}</td>
                    <td>{{ $rsvp->phone ?? '-' }}</td>
                    <td>{{ $rsvp->guests }}</td>
                    <td>{{ $rsvp->event->title ?? '-' }}</td>
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
