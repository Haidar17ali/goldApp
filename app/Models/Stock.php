<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'log_id',
        'qty',
    ];

    public function logData(){
        return $this->belongsTo(Log::class, 'log_id');
    }
}
