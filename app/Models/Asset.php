<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'type',
    ];

    /**
     * The users that belong to the asset.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('balance')
            ->using(UserAsset::class)
            ->withTimestamps();
    }
}
