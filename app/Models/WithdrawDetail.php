<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawDetail extends Model
{
    protected $fillable = [

        'withdraw_id',

        'transaction_id',

        'amount',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relation
    |--------------------------------------------------------------------------
    */

    public function withdraw()
    {
        return $this->belongsTo(Withdraw::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
