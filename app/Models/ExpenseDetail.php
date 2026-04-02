<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    protected $fillable = [
        'expense_id',
        'item_name',
        'note',
        'amount',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
