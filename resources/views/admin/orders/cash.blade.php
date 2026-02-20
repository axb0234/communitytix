@extends('admin.layout')
@section('page-title', 'Cash Collections')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <form method="GET">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" style="min-width:140px;max-width:200px;" placeholder="Search event..." value="{{ request('search') }}">
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
            <a href="{{ route('admin.orders.cash.export', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i>Export</a>
        </div>

        @if($totalAmount > 0)
        <div class="alert alert-info py-2 mb-3">
            <i class="fas fa-money-bill-wave me-1"></i> <strong>{{ $tenant->currency }} {{ number_format($totalAmount, 2) }}</strong> total cash collected across <strong>{{ $collections->total() }}</strong> records (matching current filters)
        </div>
        @endif

        <div class="card">
            <div class="card-body p-0 table-responsive-wrap">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Event</th><th>Amount</th><th>Collected By</th><th>Date</th><th>Notes</th></tr></thead>
                    <tbody>
                        @forelse($collections as $c)
                        <tr>
                            <td>{{ Str::limit($c->event->title ?? '-', 30) }}</td>
                            <td class="fw-bold">{{ number_format($c->amount, 2) }}</td>
                            <td>{{ $c->collectedBy->name ?? '-' }}</td>
                            <td>{{ $c->collected_at->format('M j, Y g:i A') }}</td>
                            <td>{{ $c->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No cash collections recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $collections->links() }}</div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Record Cash Collection</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.cash.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event_id" class="form-select" required>
                            <option value="">Select event...</option>
                            @foreach($events as $ev)<option value="{{ $ev->id }}">{{ Str::limit($ev->title, 28) }} ({{ $ev->start_at->format('j M Y') }})</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-money-bill-wave me-1"></i>Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
