<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'balance',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Add balance to the user.
     *
     * @param int $amount
     * @param string $description
     * @return void
     */
    public function addBalance($amount, $description)
    {
        $oldBalance = $this->balance;
        $this->balance += $amount;
        $this->save();

        $this->transactions()->create([
            'old_balance' => $oldBalance,
            'new_balance' => $this->balance,
            'description' => $description,
        ]);
    }

    /**
     * Deduct balance from the user.
     *
     * @param int $amount
     * @param string $description
     * @return void
     */
    public function deductBalance($amount, $description)
    {
        $oldBalance = $this->balance;
        $this->balance -= $amount;
        $this->save();

        $this->transactions()->create([
            'old_balance' => $oldBalance,
            'new_balance' => $this->balance,
            'description' => $description,
        ]);
    }
}
