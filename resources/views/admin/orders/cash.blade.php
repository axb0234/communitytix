@extends('admin.layout')
@section('page-title', 'Cash Collections')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Event</th><th>Amount</th><th>Collected By</th><th>Date</th><th>Notes</th></tr></thead>
                    <tbody>
                        @forelse($collections as $c)
                        <tr>
                            <td>{{ $c->event->title ?? '-' }}</td>
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
                            @foreach($events as $ev)<option value="{{ $ev->id }}">{{ $ev->title }}</option>@endforeach
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
