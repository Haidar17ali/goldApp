<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'id',
        'address',
        'rt',
        'rw',
        'zip_code',
        'kelurahan',
        'kecamatan',
        'city',
    ];
}
