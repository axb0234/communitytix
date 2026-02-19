<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Event;
use App\Models\Member;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = app('current_tenant');

        $stats = [
            'total_members' => Member::count(),
            'pending_members' => Member::where('status', 'PENDING')->count(),
            'upcoming_events' => Event::where('start_at', '>=', now())->count(),
            'published_posts' => BlogPost::where('status', 'published')->count(),
            'recent_orders' => Order::whereIn('status', ['COMPLETED', 'PAID'])->count(),
            'total_revenue' => Order::whereIn('status', ['COMPLETED', 'PAID'])->sum('total_amount'),
        ];

        $recentOrders = Order::with('event')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $upcomingEvents = Event::where('start_at', '>=', now())
            ->orderBy('start_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('tenant', 'stats', 'recentOrders', 'upcomingEvents'));
    }
}
