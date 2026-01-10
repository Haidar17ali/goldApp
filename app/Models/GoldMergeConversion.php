<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldMergeConversion extends Model
{
    protected $fillable = [
        'note',
        'created_by',
        'edited_by'
    ];

    public function inputs()
    {
        return $this->hasMany(GoldMergeConversionInput::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function kadar()
    {
        return $this->belongsTo(Karat::class, 'karat_id');
    }
}
