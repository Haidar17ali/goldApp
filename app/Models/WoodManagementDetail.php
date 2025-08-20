<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoodManagementDetail extends Model
{
    protected $fillable = [
        "wood_management_id",
        "lpb_id",
        "source_diameter",
        "source_qty",
        "conversion_diameter",
        "conversion_qty",
    ];

    public function woodManagement(){
        return $this->belongsTo(WoodManagement::class, "wood_management_id");
    }

    public function lpb(){
        return $this->belongsTo(LPB::class, "lpb_id");
    }
}
