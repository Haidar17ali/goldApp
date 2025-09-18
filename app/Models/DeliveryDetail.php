<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    protected $fillable = [
        "delivery_id",
        "source_id",
        "source_type", 
        "qty"
    ];

    public function delivery(){
        return $this->belongsTo(Delivery::class, "delivery_id");
    }

    public function source()
    {
        return $this->morphTo();
    }
}
