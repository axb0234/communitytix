@extends('public.layout')
@section('title', 'Order Confirmed')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="card shadow-sm p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>
                    <h2 class="fw-bold">Thank You!</h2>
                    <p class="text-muted mt-2">Your order has been {{ $order->status === 'COMPLETED' ? 'confirmed' : 'received' }}.</p>
                    <hr>
                    <div class="text-start">
                        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p><strong>Event:</strong> {{ $event->title }}</p>
                        <p><strong>Total:</strong> {{ $order->currency }} {{ number_format($order->total_amount, 2) }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge {{ $order->status === 'COMPLETED' ? 'bg-success' : 'bg-warning' }}">{{ $order->status }}</span>
                        </p>
                    </div>
                    <p class="small text-muted mt-3">A confirmation will be sent to {{ $order->purchaser_email }}.</p>
                    <a href="{{ route('events.index') }}" class="btn btn-primary mt-3">Browse More Events</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
