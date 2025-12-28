<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createCheckoutSession($amount, $currency = 'usd', $userEmail = null)
    {
        return $this->stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Balance Topup',
                    ],
                    'unit_amount' => (int) ($amount * 100), // Stripe expects cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
            'customer_email' => $userEmail,
        ]);
    }
    
    public function getSession($sessionId)
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }
}
