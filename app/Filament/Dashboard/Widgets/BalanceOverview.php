<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BalanceOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Current Balance', '$' . number_format(auth()->user()->balance, 2))
                ->description('Your current available balance')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),
        ];
    }
}
