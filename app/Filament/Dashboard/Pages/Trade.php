<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Trade extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    
    protected string $view = 'filament.dashboard.pages.trade';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Trade';

    protected static ?string $title = 'Trade Instruments';

    public function table(Table $table): Table
    {
        return $table
            ->query(Asset::query())
            ->columns([
                TextColumn::make('symbol')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'crypto' => 'warning',
                        'stock' => 'info',
                    }),
                TextColumn::make('price')
                    ->label('Current Price')
                    ->money('USD')
                    ->state(fn (Asset $record) => app(\App\Services\MarketDataService::class)->getPrice($record))
                    ->sortable(),
            ])
            ->actions([
                \App\Filament\Actions\BuyAction::make(),
            ]);
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All'),
            'crypto' => \Filament\Resources\Pages\ListRecords\Tab::make('Crypto')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'crypto')),
            'stock' => \Filament\Resources\Pages\ListRecords\Tab::make('Stocks')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'stock')),
        ];
    }
}
