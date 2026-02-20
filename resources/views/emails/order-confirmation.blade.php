<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#2d3748;padding:30px 40px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;">{{ $tenant->name }}</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px;">
                            <h2 style="color:#2d3748;margin:0 0 10px;">Order Confirmed!</h2>
                            <p style="color:#718096;font-size:16px;line-height:1.6;margin:0 0 25px;">
                                Thank you for your purchase, {{ $order->purchaser_name }}. Your payment has been received.
                            </p>

                            {{-- Order Details --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7fafc;border-radius:6px;margin-bottom:25px;">
                                <tr>
                                    <td style="padding:20px;">
                                        <p style="margin:0 0 8px;color:#4a5568;font-size:14px;">
                                            <strong>Order Number:</strong> {{ $order->order_number }}
                                        </p>
                                        <p style="margin:0 0 8px;color:#4a5568;font-size:14px;">
                                            <strong>Event:</strong> {{ $order->event->title }}
                                        </p>
                                        <p style="margin:0 0 8px;color:#4a5568;font-size:14px;">
                                            <strong>Date:</strong> {{ $order->event->start_at->format('l, j F Y \a\t g:i A') }}
                                        </p>
                                        @if($order->event->location)
                                        <p style="margin:0 0 8px;color:#4a5568;font-size:14px;">
                                            <strong>Venue:</strong> {{ $order->event->location }}
                                        </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            {{-- Items --}}
                            <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom:25px;border-collapse:collapse;">
                                <thead>
                                    <tr style="border-bottom:2px solid #e2e8f0;">
                                        <th align="left" style="color:#4a5568;font-size:13px;text-transform:uppercase;padding-bottom:10px;">Item</th>
                                        <th align="center" style="color:#4a5568;font-size:13px;text-transform:uppercase;padding-bottom:10px;">Qty</th>
                                        <th align="right" style="color:#4a5568;font-size:13px;text-transform:uppercase;padding-bottom:10px;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr style="border-bottom:1px solid #e2e8f0;">
                                        <td style="color:#2d3748;font-size:14px;padding:10px 8px;">
                                            {{ $item->ticket_type_id ? ($item->ticketType->name ?? '-') : 'Pay What You Can' }}
                                        </td>
                                        <td align="center" style="color:#2d3748;font-size:14px;padding:10px 8px;">{{ $item->qty }}</td>
                                        <td align="right" style="color:#2d3748;font-size:14px;padding:10px 8px;">{{ $order->currency }} {{ number_format($item->unit_price * $item->qty, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" align="right" style="padding:12px 8px;font-weight:bold;color:#2d3748;font-size:16px;">Total:</td>
                                        <td align="right" style="padding:12px 8px;font-weight:bold;color:#2d3748;font-size:16px;">{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <p style="color:#718096;font-size:14px;line-height:1.6;margin:0;">
                                If you have any questions about your order, please contact us at
                                <a href="mailto:{{ $tenant->contact_email }}" style="color:#4299e1;">{{ $tenant->contact_email }}</a>.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f7fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                            <p style="color:#a0aec0;font-size:12px;margin:0;">
                                &copy; {{ date('Y') }} {{ $tenant->name }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
