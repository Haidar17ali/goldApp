<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
            "date",
            "sender",
            "arrival_date",
            "create_by",
            "edit_by",
    ];

    public function details(){
        return $this->hasMany(DeliveryDetail::class);
    }
    
    public function createBy(){
        return $this->belongsTo(User::class, "create_by");
    }

    public function editBy(){
        return $this->belongsTo(User::class, "edit_by");
    }
}
