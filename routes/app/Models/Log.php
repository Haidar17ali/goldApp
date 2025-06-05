<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'id_produksi',
        'barcode',
        'code',
        'type',
        'quality',
        'length',
        'diameter',
        'quantity',
    ];

    public function stock(){
        return $this->hasOne(Stock::class, 'log_id');
    }
}
