<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
