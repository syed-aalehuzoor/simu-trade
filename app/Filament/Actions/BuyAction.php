<?php

namespace App\Filament\Actions;

use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class BuyAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'buy';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Buy')
            ->button()
            ->color('success')
            ->form([
                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0.00000001)
                    ->suffix('Units'),
            ])
            ->action(function (Asset $record, array $data): void {
                $user = Auth::user();
                $quantity = $data['quantity'];
                $price = app(\App\Services\MarketDataService::class)->getPrice($record);
                $totalCost = $price * $quantity;

                if ($user->balance < $totalCost) {
                    Notification::make()
                        ->title('Insufficient Funds')
                        ->body("You need $" . number_format($totalCost, 2) . " but only have $" . number_format($user->balance, 2) . ".")
                        ->danger()
                        ->send();
                    
                    return;
                }

                // Deduct balance
                $user->deductBalance($totalCost, "Bought {$quantity} {$record->symbol}");

                // Add to pivot
                $currentAsset = $user->assets()->where('asset_id', $record->id)->first();
                if ($currentAsset) {
                    $user->assets()->updateExistingPivot($record->id, [
                        'balance' => $currentAsset->pivot->balance + $quantity,
                    ]);
                } else {
                    $user->assets()->attach($record->id, ['balance' => $quantity]);
                }

                Notification::make()
                    ->title('Purchase Successful')
                    ->body("Bought {$quantity} {$record->symbol} for $" . number_format($totalCost, 2))
                    ->success()
                    ->send();
            });
    }
}
