<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Down_payment extends Model
{
    protected $fillable = [
        'supplier_id',
        'pu_id',
        'nominal',
        'date',
        'type',
        'status',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
