<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSupplier extends Model
{
    protected $fillable = [
        "name",
        "phone_number",
        "address",
        "type",
    ];
}
