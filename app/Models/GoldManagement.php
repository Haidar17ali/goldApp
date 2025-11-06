<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldManagement extends Model
{
    protected $fillable = [
            "date",
            "type",
            "product_id",
            "karat_id",
            "gram_in",
            "gram_out",
            "note",
            "created_by",
            "edited_by",
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function karat(){
        return $this->belongsTo(Karat::class);
    }

    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }

}
