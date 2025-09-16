<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cutting extends Model
{
    protected $fillable = [
            "code",
            "date",
            "tailor_name",
    ];

    public function details(){
        return $this->hasMany(CuttingDetail::class);
    }
}
