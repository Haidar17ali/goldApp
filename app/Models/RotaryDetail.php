<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RotaryDetail extends Model
{
    protected $fillable = [
            "rotary_id",
            "no_kitir", //no kitir baru
            "height",
            "width",
            "length",
            "qty",
            "grade",
    ];

    public function rotary()
    {
        return $this->belongsTo(Rotary::class);
    }

}
