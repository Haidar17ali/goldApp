<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'date',
        'reference',
        'description',
        'source_type',
        'source_id'
    ];

    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }
}
