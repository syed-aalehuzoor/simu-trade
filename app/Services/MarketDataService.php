<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MarketDataService
{
    protected CoinGeckoService $coinGeckoService;
    protected StockPriceService $stockPriceService;

    public function __construct(CoinGeckoService $coinGeckoService, StockPriceService $stockPriceService)
    {
        $this->coinGeckoService = $coinGeckoService;
        $this->stockPriceService = $stockPriceService;
    }

    public function getPrice(Asset $asset): float
    {
        return Cache::remember("price_{$asset->id}", 60, function () use ($asset) {
            if ($asset->type === 'crypto') {
                return $this->getCryptoPrice($asset->symbol);
            } else {
                return $this->getStockPrice($asset->symbol);
            }
        });
    }

    protected function getCryptoPrice(string $symbol): float
    {
        // Simple mapping for demo. In production, store API ID in DB.
        $idMap = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'SOL' => 'solana',
            'ADA' => 'cardano',
            'XRP' => 'ripple',
            'BNB' => 'binancecoin',
            'DOGE' => 'dogecoin',
            'DOT' => 'polkadot',
        ];

        $id = $idMap[strtoupper($symbol)] ?? null;

        if (!$id) {
            return 0.0;
        }

        try {
            $response = Http::get("https://api.coingecko.com/api/v3/simple/price", [
                'ids' => $id,
                'vs_currencies' => 'usd',
            ]);

            return $response->json()[$id]['usd'] ?? 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    protected function getStockPrice(string $symbol): float
    {
        // Reuse StockPriceService logic but for single symbol
        // For efficiency, we might want to batch fetch in real app, 
        // but for this task, scalar fetch per asset is acceptable.
        
        // Actually, StockPriceService::getStockQuotes returns an array.
        // Let's use it properly.
        $quotes = $this->stockPriceService->getStockQuotes([$symbol]);
        
        foreach ($quotes as $quote) {
            if ($quote['symbol'] === $symbol) {
                return $quote['price'];
            }
        }

        return 0.0;
    }
}
