<?php

namespace App\Filament\Actions;

use App\Models\Asset;
use App\Services\MarketDataService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SellAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sell';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Sell')
            ->button()
            ->color('danger')
            ->form([
                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->label('Quantity to Sell')
                    ->minValue(0.00000001)
                    ->maxValue(fn (Asset $record) => $record->quantity) // Limit to currently owned quantity
                    ->suffix('Units'),
            ])
            ->action(function (Asset $record, array $data) {
                $user = Auth::user();
                $quantity = $data['quantity'];
                
                // Security check: ensure user actually owns this amount
                // Re-fetch pivot to be sure
                $userAsset = $user->assets()->where('asset_id', $record->id)->first();
                
                if (!$userAsset || $userAsset->pivot->balance < $quantity) {
                        Notification::make()
                        ->title('Insufficient Assets')
                        ->body("You do not own enough of this asset to sell {$quantity} units.")
                        ->danger()
                        ->send();
                    return;
                }

                $price = app(MarketDataService::class)->getPrice($record);
                $totalValue = $price * $quantity;

                // Credit User Balance
                $user->addBalance($totalValue, "Sold {$quantity} {$record->symbol}");

                // Deduct Asset Balance
                $newBalance = $userAsset->pivot->balance - $quantity;
                
                if ($newBalance > 0) {
                    $user->assets()->updateExistingPivot($record->id, [
                        'balance' => $newBalance,
                    ]);
                } else {
                    $user->assets()->detach($record->id);
                }

                Notification::make()
                    ->title('Sale Successful')
                    ->body("Sold {$quantity} {$record->symbol} for $" . number_format($totalValue, 2))
                    ->success()
                    ->send();
            });
    }
}
