<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'type', 'parent'];

    public function parent(){
        return $this->belongsTo(Position::class, 'parent');
    }

    public function children()
    {
        return $this->hasMany(Position::class, 'parent');
    }
}
