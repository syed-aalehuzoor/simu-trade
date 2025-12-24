<?php

namespace App\Filament\Dashboard\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('amount')
                    ->state(fn (Transaction $record): string => $record->new_balance - $record->old_balance)
                    ->money('USD')
                    ->color(fn (string $state): string => $state < 0 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('new_balance')
                    ->label('Balance')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ]);
    }
}
