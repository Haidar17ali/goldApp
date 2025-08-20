<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rotary extends Model
{
    protected $fillable = [
            "date",
            "shift",
            "type",
            "wood_type",
            "tally_id",
            "created_by",
            "edited_by",
    ];

    public function tally(){
        return $this->belongsTo(Employee::class, "tally_id");
    }

    public function details(){
        return $this->hasMany(RotaryDetail::class, "rotary_id");
    }
    
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editedBy(){
        return $this->belongsTo(User::class, 'edited_by');
    }
    
    public function rotariSources()
    {
        return $this->hasMany(RotarySource::class);
    }

}
