<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PayPalSetting;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request, string $tenantSlug)
    {
        $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            return response()->json(['error' => 'Unknown tenant'], 404);
        }

        $paypalSetting = PayPalSetting::where('tenant_id', $tenant->id)->first();
        if (!$paypalSetting) {
            return response()->json(['error' => 'No PayPal config'], 400);
        }

        // Verify webhook signature
        if ($paypalSetting->webhook_id) {
            $headers = [
                'PAYPAL-AUTH-ALGO' => $request->header('PAYPAL-AUTH-ALGO'),
                'PAYPAL-CERT-URL' => $request->header('PAYPAL-CERT-URL'),
                'PAYPAL-TRANSMISSION-ID' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'PAYPAL-TRANSMISSION-SIG' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'PAYPAL-TRANSMISSION-TIME' => $request->header('PAYPAL-TRANSMISSION-TIME'),
            ];

            $paypalService = new PayPalService($paypalSetting);
            if (!$paypalService->verifyWebhookSignature($headers, $request->getContent())) {
                Log::warning('PayPal webhook signature verification failed', ['tenant' => $tenantSlug]);
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $event = $request->input('event_type');
        $resource = $request->input('resource', []);

        Log::info('PayPal webhook received', ['event' => $event, 'tenant' => $tenantSlug]);

        switch ($event) {
            case 'CHECKOUT.ORDER.APPROVED':
                $this->handleOrderApproved($resource, $tenant->id);
                break;
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handleCaptureCompleted($resource, $tenant->id);
                break;
            case 'PAYMENT.CAPTURE.DENIED':
            case 'PAYMENT.CAPTURE.DECLINED':
                $this->handleCaptureFailed($resource, $tenant->id);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleOrderApproved(array $resource, int $tenantId): void
    {
        $orderId = $resource['id'] ?? null;
        if (!$orderId) return;

        $order = Order::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('provider_order_id', $orderId)
            ->first();

        if ($order && $order->status === 'PENDING') {
            // The capture will be done by the return URL handler
            Log::info('PayPal order approved', ['order' => $order->order_number]);
        }
    }

    private function handleCaptureCompleted(array $resource, int $tenantId): void
    {
        $captureId = $resource['id'] ?? null;

        // Find order by looking up the supplementary data or by capture ID
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        $order = null;
        if ($orderId) {
            $order = Order::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('provider_order_id', $orderId)
                ->first();
        }

        if (!$order && $captureId) {
            $order = Order::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('provider_capture_id', $captureId)
                ->first();
        }

        if ($order && $order->status !== 'COMPLETED') {
            $order->update([
                'status' => 'COMPLETED',
                'provider_capture_id' => $captureId,
                'paid_at' => now(),
            ]);
            Log::info('Order completed via webhook', ['order' => $order->order_number]);
        }
    }

    private function handleCaptureFailed(array $resource, int $tenantId): void
    {
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if ($orderId) {
            $order = Order::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('provider_order_id', $orderId)
                ->first();

            if ($order) {
                $order->update(['status' => 'FAILED']);
            }
        }
    }
}
