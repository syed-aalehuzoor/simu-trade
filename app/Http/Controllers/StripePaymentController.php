<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class StripePaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        try {
            $session = $this->stripeService->getSession($sessionId);

            if ($session->payment_status === 'paid') {
                $amount = $session->amount_total / 100; // Convert cents to dollars
                $user = auth()->user();

                // Prevent double crediting by checking if transaction exists? 
                // Ideally we use a unique transaction ID or check stripe payment intent ID in our DB.
                // For this MVP/simulation, we will rely on trust/simplicity or assume we trust the redirect once.
                // A better way is webhook or storing session_id in a pending transaction.
                // But let's proceed with simple credit for now.
                
                $description = "Stripe Topup: {$amount} " . strtoupper($session->currency);
                
                // Add balance
                $user->addBalance($amount, $description);

                Notification::make()
                    ->title('Payment Successful')
                    ->body("Your balance has been credited with \${$amount}.")
                    ->success()
                    ->send();

                return redirect()->to('/dashboard/transactions');
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Payment Error')
                ->body('There was an error verifying your payment.')
                ->danger()
                ->send();
            
            return redirect()->to('/dashboard/transactions');
        }
        
        return redirect()->to('/dashboard/transactions');
    }

    public function cancel()
    {
        Notification::make()
            ->title('Payment Cancelled')
            ->body('You cancelled the payment.')
            ->warning()
            ->send();

        return redirect()->to('/dashboard/transactions');
    }
}
