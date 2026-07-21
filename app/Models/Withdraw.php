<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = [

        'marketplace_id',

        'receive_at',

        'transaction_date',

        'bank_account_id',

        'total',

        'note',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'receive_at' => 'date',

        'transaction_date' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relation
    |--------------------------------------------------------------------------
    */

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function details()
    {
        return $this->hasMany(WithdrawDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getInvoiceCountAttribute()
    {
        return $this->details()->count();
    }

    public function getStatusAttribute()
    {
        if (!$this->receive_at) {
            return 'piutang';
        }

        if ($this->receive_at && !$this->transaction_date) {
            return 'kas_online';
        }

        return 'withdraw';
    }
}
