<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class npwp extends Model
{
    protected $fillable = [
        'npwp',
        'nitku',
        'name',
    ];

    public function lpbs() {
        return $this->hasMany(LPB::class);
    }
}
