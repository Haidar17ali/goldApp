<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karat extends Model
{
    protected $fillable = [
            "name",
    ];

    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }

    public function stocks(){
        return $this->hasMany(\App\Models\Stock::class);
    }

}
