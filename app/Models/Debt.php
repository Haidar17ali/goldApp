<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        "tailor",
        "source_id",
        "cutting_detail_id",
        "from",
        "qty",
        "status",
    ];

    public function deliveryDetails(){
        return $this->morphMany(DeliveryDetail::class, "source");
    }

    public function cuttingDetail()
    {
        return $this->belongsTo(CuttingDetail::class);
    }
}
