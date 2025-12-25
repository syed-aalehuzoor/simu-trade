<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CoinGeckoStats extends BaseWidget
{
    // protected static ?string $pollingInterval = '60s'; // Optional: poll every 60s, but cache handles rate limit

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        $service = new \App\Services\CoinGeckoService();
        $coins = $service->getTopCoins();

        if (empty($coins)) {
            return [
                Stat::make('Error', 'Unable to fetch data')
                    ->description('Please try again later')
                    ->color('danger'),
            ];
        }

        $stats = [];

        foreach ($coins as $coin) {
            $price = number_format($coin['current_price'], 2);
            $change = $coin['price_change_percentage_24h'];
            $isPositive = $change >= 0;
            $color = $isPositive ? 'success' : 'danger';
            $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
            $chartColor = $isPositive ? 'success' : 'danger'; // Simple color mapping

            // Sparkline data needs to be an array of numbers
            $chartData = $coin['sparkline_in_7d']['price'] ?? [];

            $stats[] = Stat::make(strtoupper($coin['symbol']), '$' . $price)
                ->description(number_format($change, 2) . '%')
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($chartData);
        }

        return $stats;
    }
}
