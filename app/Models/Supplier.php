<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'npwp_number',
        'nitku',
        'nik',
        'supplier_type',
        'name',
        'address_id',
        'bank_id',
    ];

    
    public function address(){
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
