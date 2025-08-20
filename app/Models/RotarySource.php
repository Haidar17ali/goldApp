<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RotarySource extends Model
{
    protected $fillable = [
            "rotary_id",
            "source_id",
            "source_type",
    ];

    public function source()
    {
        return $this->morphTo();
    }
}
