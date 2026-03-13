<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        "code",
        "name",
        "address"
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
