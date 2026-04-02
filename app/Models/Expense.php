<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'code',
        'branch_id',
        'date',
        'description',
        'total_amount',
        'created_by'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(ExpenseDetail::class);
    }
}
