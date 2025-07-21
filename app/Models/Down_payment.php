<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Down_payment extends Model
{
    protected $fillable = [
            'supplier_id',
            'date',
            'nota_date',
            'arrival_date',
            'type',
            'dp_type',
            'nominal',
            'parent_id',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function details(){
        return $this->hasMany(DP_detail::class, 'dp_id');
    }

    // Untuk mengambil anak-anak dari DP ini
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Untuk mengambil induknya
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
