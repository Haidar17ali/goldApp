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
        'source_id',
        'reversal_of',
        'is_reversal',
    ];

    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    public function reversedBy()
    {
        return $this->hasOne(Journal::class, 'reversal_of');
    }

    public function original()
    {
        return $this->belongsTo(Journal::class, 'reversal_of');
    }
}
