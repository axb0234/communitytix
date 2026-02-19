@extends('admin.layout')
@section('page-title', 'Orders')

@section('content')
<form class="d-flex gap-2 mb-3" method="GET">
    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name/email/order#..." value="{{ request('search') }}">
    <select name="status" class="form-select form-select-sm" style="width:140px;" onchange="this.form.submit()">
        <option value="">All Status</option>
        @foreach(['PENDING','COMPLETED','PAID','REFUNDED','CANCELLED','FAILED'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
    </select>
    <select name="event_id" class="form-select form-select-sm" style="width:200px;" onchange="this.form.submit()">
        <option value="">All Events</option>
        @foreach($events as $ev)
        <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ Str::limit($ev->title, 30) }}</option>
        @endforeach
    </select>
    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Order #</th><th>Purchaser</th><th>Event</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td>{{ $order->purchaser_name }}<br><small class="text-muted">{{ $order->purchaser_email }}</small></td>
                    <td>{{ Str::limit($order->event->title ?? '-', 25) }}</td>
                    <td>{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</td>
                    <td><span class="badge bg-{{ $order->status === 'COMPLETED' ? 'success' : ($order->status === 'REFUNDED' ? 'danger' : ($order->status === 'PENDING' ? 'warning' : 'secondary')) }}">{{ $order->status }}</span></td>
                    <td>{{ $order->created_at->format('M j, Y') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
