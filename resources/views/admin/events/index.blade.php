@extends('admin.layout')
@section('page-title', 'Events')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
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
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Event</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Event</th><th>Date</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td><a href="{{ route('admin.events.edit', $event) }}">{{ $event->title }}</a></td>
                    <td>{{ $event->start_at->format('M j, Y g:i A') }}</td>
                    <td><span class="badge bg-{{ $event->event_type === 'TICKETED' ? 'warning text-dark' : 'success' }}">{{ $event->event_type }}</span></td>
                    <td><span class="badge bg-{{ $event->status === 'published' ? 'success' : 'secondary' }}">{{ $event->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="d-inline" onsubmit="return confirm('Delete this event?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No events yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $events->links() }}</div>
@endsection
