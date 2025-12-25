<?php

namespace App\Filament\Dashboard\Widgets;

use App\Services\StockPriceService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockPriceWidget extends BaseWidget
{
    // protected static ?string $pollingInterval = '60s';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        $service = new StockPriceService();
        $stocks = $service->getStockQuotes();

        if (empty($stocks)) {
            return [
                Stat::make('Error', 'Unable to fetch stock data')
                    ->description('Check API Key or try again later')
                    ->color('danger'),
            ];
        }

        $stats = [];

        foreach ($stocks as $stock) {
            $price = number_format($stock['price'], 2);
            $change = $stock['change_percent'];
            $isPositive = $change >= 0;
            $color = $isPositive ? 'success' : 'danger';
            $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

            $stats[] = Stat::make($stock['symbol'], '$' . $price)
                ->description(number_format($change, 2) . '%')
                ->descriptionIcon($icon)
                ->color($color);
        }

        return $stats;
    }
}
