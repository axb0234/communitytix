<?php

namespace App\Services;

use App\Models\PayPalSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private PayPalSetting $settings;
    private ?string $accessToken = null;

    public function __construct(PayPalSetting $settings)
    {
        $this->settings = $settings;
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::withBasicAuth(
            $this->settings->client_id_decrypted,
            $this->settings->client_secret_decrypted
        )->asForm()->post($this->settings->base_url . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);

        if (!$response->successful()) {
            Log::error('PayPal auth failed', ['response' => $response->body()]);
            throw new \RuntimeException('Failed to authenticate with PayPal');
        }

        $this->accessToken = $response->json('access_token');
        return $this->accessToken;
    }

    public function createOrder(array $items, string $currency, string $returnUrl, string $cancelUrl): array
    {
        $total = collect($items)->sum(fn($item) => $item['quantity'] * $item['unit_price']);

        $orderItems = collect($items)->map(fn($item) => [
            'name' => $item['name'],
            'quantity' => (string)$item['quantity'],
            'unit_amount' => [
                'currency_code' => $currency,
                'value' => number_format($item['unit_price'], 2, '.', ''),
            ],
        ])->toArray();

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $currency,
                    'value' => number_format($total, 2, '.', ''),
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => $currency,
                            'value' => number_format($total, 2, '.', ''),
                        ],
                    ],
                ],
                'items' => $orderItems,
            ]],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                        'brand_name' => app()->bound('current_tenant') ? app('current_tenant')->name : 'CommunityTix',
                        'user_action' => 'PAY_NOW',
                        'payment_method_preference' => 'UNRESTRICTED',
                    ],
                ],
            ],
        ];

        $response = Http::withToken($this->getAccessToken())
            ->post($this->settings->base_url . '/v2/checkout/orders', $payload);

        if (!$response->successful()) {
            Log::error('PayPal create order failed', ['response' => $response->body()]);
            throw new \RuntimeException('Failed to create PayPal order');
        }

        return $response->json();
    }

    public function captureOrder(string $orderId): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->withBody('{}', 'application/json')
            ->post($this->settings->base_url . "/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            Log::error('PayPal capture failed', ['response' => $response->body()]);
            throw new \RuntimeException('Failed to capture PayPal order');
        }

        return $response->json();
    }

    public function getOrderDetails(string $orderId): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->get($this->settings->base_url . "/v2/checkout/orders/{$orderId}");

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to get PayPal order details');
        }

        return $response->json();
    }

    public function verifyWebhookSignature(array $headers, string $body): bool
    {
        if (!$this->settings->webhook_id) {
            return false;
        }

        $payload = [
            'auth_algo' => $headers['PAYPAL-AUTH-ALGO'] ?? '',
            'cert_url' => $headers['PAYPAL-CERT-URL'] ?? '',
            'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
            'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
            'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
            'webhook_id' => $this->settings->webhook_id,
            'webhook_event' => json_decode($body, true),
        ];

        $response = Http::withToken($this->getAccessToken())
            ->post($this->settings->base_url . '/v1/notifications/verify-webhook-signature', $payload);

        if (!$response->successful()) {
            Log::error('PayPal webhook verification failed', ['response' => $response->body()]);
            return false;
        }

        return $response->json('verification_status') === 'SUCCESS';
    }
}
