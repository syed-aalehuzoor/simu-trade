<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CoinGeckoService
{
    /**
     * Fetch top coins from CoinGecko API.
     * Caches the result for 5 minutes.
     *
     * @param int $limit
     * @return array
     */
    public function getTopCoins(int $limit = 5): array
    {
        return Cache::remember('coingecko_stats', 300, function () use ($limit) {
            try {
                $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
                    'vs_currency' => 'usd',
                    'order' => 'market_cap_desc',
                    'per_page' => $limit,
                    'page' => 1,
                    'sparkline' => 'true',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (\Exception $e) {
                // Log error if needed
                return [];
            }
        });
    }
}
