@extends('admin.layout')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card"><div class="stat-number text-primary">{{ $stats['total_members'] }}</div><div class="stat-label">Members</div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card"><div class="stat-number text-warning">{{ $stats['pending_members'] }}</div><div class="stat-label">Pending</div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card"><div class="stat-number text-info">{{ $stats['upcoming_events'] }}</div><div class="stat-label">Upcoming Events</div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card"><div class="stat-number text-success">{{ $stats['published_posts'] }}</div><div class="stat-label">Blog Posts</div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card"><div class="stat-number text-danger">{{ $stats['recent_orders'] }}</div><div class="stat-label">Orders</div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card"><div class="stat-number text-success">{{ $tenant->currency }} {{ number_format($stats['total_revenue'], 2) }}</div><div class="stat-label">Revenue</div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Order</th><th>Event</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ Str::limit($order->event->title ?? '', 25) }}</td>
                            <td>{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $order->status === 'COMPLETED' ? 'success' : ($order->status === 'PENDING' ? 'warning' : 'secondary') }}">{{ $order->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Upcoming Events</h5>
                <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Event</th><th>Date</th><th>Type</th></tr></thead>
                    <tbody>
                        @forelse($upcomingEvents as $event)
                        <tr>
                            <td><a href="{{ route('admin.events.edit', $event) }}">{{ Str::limit($event->title, 30) }}</a></td>
                            <td>{{ $event->start_at->format('M j, Y') }}</td>
                            <td><span class="badge bg-{{ $event->event_type === 'TICKETED' ? 'warning' : 'success' }}">{{ $event->event_type }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">No upcoming events</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
