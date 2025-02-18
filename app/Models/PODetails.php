<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PODetails extends Model
{
    protected $fillable = [
        'po_id',
        'name',
        'diameter_start',
        'diameter_to',
        'quantity',
        'price',
    ];
}
