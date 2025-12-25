<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\Asset;
use App\Services\MarketDataService;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MyAssets extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.dashboard.pages.my-assets';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->join('asset_user', 'assets.id', '=', 'asset_user.asset_id')
                    ->where('asset_user.user_id', Auth::id())
                    ->select('assets.*', 'asset_user.balance as quantity')
            )
            ->columns([
                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'crypto' => 'warning',
                        'stock' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric(),
                TextColumn::make('price')
                    ->label('Current Price')
                    ->money('USD')
                    ->state(fn (Asset $record, MarketDataService $service) => $service->getPrice($record)),
                TextColumn::make('total_value')
                    ->label('Total Value')
                    ->money('USD')
                    ->state(function (Asset $record, MarketDataService $service) {
                        $price = $service->getPrice($record);
                        // Access the quantity from the pivot or the selected alias
                        // Since we did a join and select, it should be on the model attribute 'quantity'.
                        // However, Eloquent might hide it if it's not in fillable or appends.
                        // Let's use the raw attribute.
                        $quantity = $record->quantity; 
                        return $price * $quantity;
                    }),
            ]);
    }
}
