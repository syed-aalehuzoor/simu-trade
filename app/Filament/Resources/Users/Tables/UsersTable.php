<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('balance')
                    ->money(currency: 'USD') // Assuming USD, or just numeric
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('manageBalance')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Select::make('type')
                            ->options([
                                'add' => 'Add',
                                'deduct' => 'Deduct',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->numeric()
                            ->required(),
                        TextInput::make('description')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data): void {
                        if ($data['type'] === 'add') {
                            $record->addBalance((int) $data['amount'], $data['description']);
                        } else {
                            $record->deductBalance((int) $data['amount'], $data['description']);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
