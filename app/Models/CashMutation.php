<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashMutation extends Model
{
    protected $fillable = [
        'date',
        'reference',
        'from_bank_account_id',
        'to_bank_account_id',
        'amount',
        'note',
    ];

    public function fromBank()
    {
        return $this->belongsTo(BankAccount::class, 'from_bank_account_id');
    }

    public function toBank()
    {
        return $this->belongsTo(BankAccount::class, 'to_bank_account_id');
    }
}
