<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StockPriceService
{
    /**
     * Fetch stock quotes for given symbols from Finnhub.
     * Caches the result for 5 minutes.
     *
     * @param array $symbols
     * @return array
     */
    public function getStockQuotes(array $symbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA']): array
    {
        $apiKey = env('FINNHUB_API_KEY');

        if (empty($apiKey)) {
            return [];
        }

        return Cache::remember('finnhub_stocks', 300, function () use ($symbols, $apiKey) {
            $data = [];

            foreach ($symbols as $symbol) {
                try {
                    $response = Http::get('https://finnhub.io/api/v1/quote', [
                        'symbol' => $symbol,
                        'token' => $apiKey,
                    ]);

                    if ($response->successful()) {
                        $quote = $response->json();
                        // Finnhub returns 'c' for current price, 'dp' for percent change
                        if (isset($quote['c']) && isset($quote['dp'])) {
                            $data[] = [
                                'symbol' => $symbol,
                                'price' => $quote['c'],
                                'change_percent' => $quote['dp'],
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // Continue to next symbol on error
                    continue;
                }
            }

            return $data;
        });
    }
}
