<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PODetails extends Model
{
    protected $fillable = [
        'po_id',
        'name',
        'quality',
        'length',
        'diameter_start',
        'diameter_to',
        'quantity',
        'price',
    ];

    public function PO(){
        return $this->belongsTo(PO::class, "po_id");
    }
}
