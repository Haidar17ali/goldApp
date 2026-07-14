<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferStock extends Model
{
    protected $fillable = [
        'transfer_date',
        'from_branch_id',
        'to_branch_id',
        'note',
        'status',
        'created_by',
        'edited_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function details()
    {
        return $this->hasMany(TransferStockDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
