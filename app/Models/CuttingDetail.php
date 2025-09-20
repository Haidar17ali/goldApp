<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuttingDetail extends Model
{
    protected $fillable = [
            "cutting_id",
            "product_id",
            "color_id",
            "size_id",
            "qty",
            "finish_at",
            "status",
    ];

    public function cutting(){
        return $this->belongsTo(Cutting::class, "cutting_id");
    }

    public function product(){
        return $this->belongsTo(Product::class, "product_id");
    }

    public function color(){
        return $this->belongsTo(Color::class, "color_id");
    }

    public function size(){
        return $this->belongsTo(Size::class, "size_id");
    }

    public function deliveryDetails(){
        return $this->morphMany(DeliveryDetail::class, "source");
    }

    public function debt(){
        return $this->hasMany(Debt::class);
    }
}
