<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'is_active',
    ];

    public function transactionMarketplaces()
    {
        return $this->hasMany(TransactionMarketplace::class);
    }
}
