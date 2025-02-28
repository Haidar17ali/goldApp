<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseJurnal extends Model
{
    protected $fillable =[
        'date',
        'created_by',
        'edited_by',
        'status',
    ];

}
