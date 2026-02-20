<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CashCollection;
use App\Models\Event;
use App\Models\Order;
use App\Models\PosPayment;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    private function buildOrderQuery(Request $request)
    {
        $query = Order::with(['event'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('purchaser_email', 'ilike', '%' . $request->search . '%')
                  ->orWhere('purchaser_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('order_number', 'ilike', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $orders = $this->buildOrderQuery($request)->paginate(20)->withQueryString();
        $events = Event::orderByDesc('start_at')->get();
        return view('admin.orders.index', compact('orders', 'events'));
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = $this->buildOrderQuery($request)->get();
        $tenant = app('current_tenant');
        $filename = Str::slug($tenant->name) . '-orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders, $tenant) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Order #', 'Purchaser Name', 'Email', 'Phone',
                'Event', 'Amount (' . $tenant->currency . ')', 'Payment Method',
                'Status', 'Order Date', 'Paid At',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->purchaser_name,
                    $order->purchaser_email,
                    $order->purchaser_phone ?? '',
                    $order->event->title ?? '-',
                    number_format($order->total_amount, 2, '.', ''),
                    $order->payment_method,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->paid_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items.ticketType', 'event');
        return view('admin.orders.show', compact('order'));
    }

    public function markRefunded(Order $order)
    {
        $order->update([
            'status' => 'REFUNDED',
            'refunded_at' => now(),
            'refunded_by' => auth()->id(),
        ]);

        AuditLog::log('order_refunded', 'Order', $order->id, [
            'order_number' => $order->order_number,
            'amount' => $order->total_amount,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order marked as refunded.');
    }

    // RSVPs
    public function rsvps(Request $request)
    {
        $query = Rsvp::with('event')->orderByDesc('created_at');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $rsvps = $query->paginate(20)->withQueryString();
        $events = Event::where('event_type', 'FREE')->orderByDesc('start_at')->get();
        return view('admin.orders.rsvps', compact('rsvps', 'events'));
    }

    // Cash Collections
    public function cashIndex(Request $request)
    {
        $query = CashCollection::with(['event', 'collectedBy'])->orderByDesc('collected_at');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $collections = $query->paginate(20)->withQueryString();
        $events = Event::orderByDesc('start_at')->get();
        return view('admin.orders.cash', compact('collections', 'events'));
    }

    public function storeCash(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['collected_by_user_id'] = auth()->id();
        $data['collected_at'] = now();

        CashCollection::create($data);
        AuditLog::log('cash_collection_recorded', 'CashCollection', null, $data);

        return redirect()->route('admin.orders.cash')->with('success', 'Cash collection recorded.');
    }

    // POS Payments (card at door)
    public function posIndex(Request $request)
    {
        $query = PosPayment::with(['event', 'recordedBy'])->orderByDesc('recorded_at');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $payments = $query->paginate(20)->withQueryString();
        $events = Event::orderByDesc('start_at')->get();
        return view('admin.orders.pos', compact('payments', 'events'));
    }

    public function storePos(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['method'] = 'CARD';
        $data['status'] = 'PENDING_RECONCILIATION';
        $data['recorded_by_user_id'] = auth()->id();
        $data['recorded_at'] = now();

        PosPayment::create($data);
        AuditLog::log('pos_payment_recorded', 'PosPayment', null, $data);

        return redirect()->route('admin.orders.pos')->with('success', 'Card payment recorded for reconciliation.');
    }
}
