<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'type', 'parent_id'];

    public function parent(){
        return $this->belongsTo(Position::class, "parent_id");
    }

    public function children()
    {
        return $this->hasMany(Position::class, 'parent_id');
    }
}
