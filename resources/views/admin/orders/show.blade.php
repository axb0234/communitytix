@extends('admin.layout')
@section('page-title', 'Order ' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Order Details</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th style="width:30%">Order #</th><td>{{ $order->order_number }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $order->status === 'COMPLETED' ? 'success' : ($order->status === 'REFUNDED' ? 'danger' : 'warning') }}">{{ $order->status }}</span></td></tr>
                    <tr><th>Event</th><td>{{ $order->event->title }}</td></tr>
                    <tr><th>Purchaser</th><td>{{ $order->purchaser_name }} ({{ $order->purchaser_email }})</td></tr>
                    <tr><th>Phone</th><td>{{ $order->purchaser_phone ?? '-' }}</td></tr>
                    <tr><th>Payment Method</th><td>{{ $order->payment_method }}</td></tr>
                    <tr><th>PayPal Order ID</th><td>{{ $order->provider_order_id ?? '-' }}</td></tr>
                    <tr><th>Paid At</th><td>{{ $order->paid_at?->format('M j, Y g:i A') ?? '-' }}</td></tr>
                    @if($order->refunded_at)
                    <tr><th>Refunded At</th><td>{{ $order->refunded_at->format('M j, Y g:i A') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Items</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Ticket Type</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->ticketType->name ?? '-' }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $order->currency }} {{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $order->currency }} {{ number_format($item->qty * $item->unit_price, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td>{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Actions</h5></div>
            <div class="card-body">
                @if(in_array($order->status, ['COMPLETED', 'PAID']))
                <form method="POST" action="{{ route('admin.orders.refund', $order) }}" onsubmit="return confirm('Mark this order as refunded?')">
                    @csrf
                    <button class="btn btn-danger w-100"><i class="fas fa-undo me-1"></i>Mark as Refunded</button>
                </form>
                <small class="text-muted d-block mt-2">This marks the order as refunded in the system. Process the actual refund through PayPal separately.</small>
                @else
                <p class="text-muted">No actions available for {{ $order->status }} orders.</p>
                @endif
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary mt-3"><i class="fas fa-arrow-left me-1"></i>Back to Orders</a>
@endsection
