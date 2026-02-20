@extends('admin.layout')
@section('page-title', 'Card at Door (POS)')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <form method="GET">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" style="min-width:140px;max-width:200px;" placeholder="Search event/ref..." value="{{ request('search') }}">
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
            <a href="{{ route('admin.orders.pos.export', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i>Export</a>
        </div>

        @if($totalAmount > 0)
        <div class="alert alert-info py-2 mb-3">
            <i class="fas fa-credit-card me-1"></i> <strong>{{ $tenant->currency }} {{ number_format($totalAmount, 2) }}</strong> total card payments across <strong>{{ $payments->total() }}</strong> records (matching current filters)
        </div>
        @endif

        <div class="card">
            <div class="card-body p-0 table-responsive-wrap">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Event</th><th>Amount</th><th>Reference</th><th>Status</th><th>Recorded By</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr>
                            <td>{{ Str::limit($p->event->title ?? '-', 30) }}</td>
                            <td class="fw-bold">{{ number_format($p->amount, 2) }}</td>
                            <td>{{ $p->reference ?? '-' }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $p->status }}</span></td>
                            <td>{{ $p->recordedBy->name ?? '-' }}</td>
                            <td>{{ $p->recorded_at->format('M j, Y g:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No POS payments recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $payments->links() }}</div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Record Card Payment</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.pos.store') }}">
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
                        <label class="form-label">Reference / Receipt #</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"><i class="fas fa-credit-card me-1"></i>Record (Pending Reconciliation)</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
