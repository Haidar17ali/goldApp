<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldConversion extends Model
{
    protected $fillable = [
        'stock_id',
        'product_id',
        'karat_id',
        'input_weight',
        'note',
        'created_by',
        'edited_by'
    ];

    
     // Stok gelondongan yang diproses
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function kadar()
    {
        return $this->belongsTo(Karat::class, 'karat_id');
    }

    // Semua output item (cincin, gelang, anting)
    public function outputs()
    {
        return $this->hasMany(GoldConversionOutput::class, 'gold_conversion_id');
    }

    // User yang membuat proses ini
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
