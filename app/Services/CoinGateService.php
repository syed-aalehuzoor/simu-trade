<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CoinGateService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.coingate.api_key');
        $this->baseUrl = config('services.coingate.environment') === 'live'
            ? 'https://api.coingate.com/v2'
            : 'https://api-sandbox.coingate.com/v2';
    }

    public function createOrder($amount, $currency = 'USD', $title = 'Balance Topup', $description = null)
    {
        $description = $description ?? "Topup amount: {$amount} {$currency}";

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/orders", [
                'order_id' => 'ORDER-' . uniqid(),
                'price_amount' => $amount,
                'price_currency' => $currency,
                'receive_currency' => 'USD',
                'title' => $title,
                'description' => $description,
                'success_url' => url('/dashboard/transactions'),
                'cancel_url' => url('/dashboard/transactions'),
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('CoinGate Order Creation Failed: ' . $response->body());
    }
}
