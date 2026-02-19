@extends('admin.layout')
@section('page-title', 'Card at Door (POS)')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0 table-responsive-wrap">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Event</th><th>Amount</th><th>Reference</th><th>Status</th><th>Recorded By</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr>
                            <td>{{ $p->event->title ?? '-' }}</td>
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
                            @foreach($events as $ev)<option value="{{ $ev->id }}">{{ $ev->title }}</option>@endforeach
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
