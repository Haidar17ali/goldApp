<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cutting extends Model
{
    protected $fillable = [
            "code",
            "date",
            "tailor_name",
            "create_by",
            "edit_by",
    ];

    public function details(){
        return $this->hasMany(CuttingDetail::class);
    }

    public function createBy(){
        return $this->belongsTo(User::class, "create_by");
    }

    public function editBy(){
        return $this->belongsTo(User::class, "edit_by");
    }
}
