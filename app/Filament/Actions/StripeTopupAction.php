<?php

namespace App\Filament\Actions;

use App\Services\StripeService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class StripeTopupAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'stripe_topup';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Topup with Stripe')
            ->icon('heroicon-o-credit-card')
            ->form([
                TextInput::make('amount')
                    ->label('Amount (USD)')
                    ->numeric()
                    ->required()
                    ->minValue(1),
            ])
            ->action(function (array $data) {
                try {
                    $service = app(StripeService::class);
                    $user = auth()->user();
                    
                    $session = $service->createCheckoutSession(
                        $data['amount'], 
                        'usd', 
                        $user->email
                    );

                    if (isset($session->url)) {
                        return redirect()->away($session->url);
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
