<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = [
            "code",
            "name",
            "width",
            "length",
    ];
}
