<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadPermitDetail extends Model
{
    protected $fillable = [
        'road_permit_id',
        'load',
        'amount',
        'unit',
        'size',
        'cubication',
        'status',
    ];

    public function parent(){
        return $this->belongsTo(RoadPermit::class, 'road_permit_id');
    }
}
