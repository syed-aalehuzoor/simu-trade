<?php

namespace App\Filament\Dashboard\Resources\Transactions\Pages;

use App\Filament\Dashboard\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
