<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserAsset extends Pivot
{
    protected $table = 'asset_user';

    protected $fillable = [
        'user_id',
        'asset_id',
        'balance',
    ];
}
