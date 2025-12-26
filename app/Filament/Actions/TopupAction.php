<?php

namespace App\Filament\Actions;

use App\Models\Asset;
use App\Services\CoinGateService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class TopupAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'topup';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Topup Balance')
            ->icon('heroicon-o-currency-dollar')
            ->form([
                TextInput::make('amount')
                    ->label('Amount (USD)')
                    ->numeric()
                    ->required()
                    ->minValue(1),
            ])
            ->action(function (array $data) {
                try {
                    $service = app(CoinGateService::class);
                    
                    $title = 'Balance Topup';
                    $description = "Topup amount: {$data['amount']} USD";

                    $order = $service->createOrder(
                        $data['amount'], 
                        'USD', 
                        $title, 
                        $description
                    );

                    if (isset($order['payment_url'])) {
                        return redirect()->away($order['payment_url']);
                    }

                    Notification::make()
                        ->title('Error creating payment')
                        ->danger()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
