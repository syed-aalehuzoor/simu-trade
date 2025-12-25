<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \App\Filament\Dashboard\Widgets\CoinGeckoStats::class,
            \App\Filament\Dashboard\Widgets\StockPriceWidget::class,
        ];
    }
}