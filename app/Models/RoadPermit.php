<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadPermit extends Model
{
    protected $fillable = [
        'code',
        'date',
        'in',
        'out',
        'description',
        'handyman_id',
        'from',
        'destination',
        'vehicle',
        'nopol',
        'driver',
        'unpack_location',
        'sill_number',
        'container_number',
        'type',
        'type_item',
        'created_by',
        'edited_by',
        'issued_by',
    ];

    public function details(){
        return $this->hasMany(RoadPermitDetail::class, 'road_permit_id');
    }

    public function handyman(){
        return $this->belongsTo(Employee::class, 'handyman_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editedBy(){
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function issuedBy(){
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function LPB(){
        return $this->hasMany(LPB::class, 'road_permit_id');
    }
}
