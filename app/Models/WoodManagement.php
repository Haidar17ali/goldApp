<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoodManagement extends Model
{
    protected $fillable = [
        "date",
        "no_kitir",
        "grade",
        "type",
        "from",
        "to",
        "tally_id",
        "created_by",
        "edited_by",
    ];

    public function details(){
        return $this->hasMany(WoodManagementDetail::class, "wood_management_id");
    }

    public function tally(){
        return $this->belongsTo(Employee::class, "tally_id");
    }

     public function rotarysources()
    {
        return $this->morphMany(RotarySource::class, 'source');
    }
    
}
