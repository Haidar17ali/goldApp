<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gram extends Model
{
    protected $fillable = [
            "name",
            "weight",
    ];

    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }
}
