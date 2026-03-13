<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'gaji',
        'potongan',
        'bonus',
        'sistem_gaji',
        'hari_kerja',
        'periode',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor total gaji bersih
    public function getTotalAttribute()
    {
        return ($this->gaji + $this->bonus) - $this->potongan;
    }
}
