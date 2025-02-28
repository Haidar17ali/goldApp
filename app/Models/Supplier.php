<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'npwp_id',
        'nik',
        'supplier_type',
        'name',
        'phone',
        'address_id',
        'bank_id',
    ];

    
    public function address(){
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function npwp(){
        return $this->belongsTo(npwp::class, 'npwp_id');
    }


}
