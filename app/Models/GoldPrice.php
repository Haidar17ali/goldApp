<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GoldPrice extends Model
{
    protected $fillable = [
        'active_at',
        'expired_at',
    ];

    protected $dates = ['active_at', 'expired_at'];

    public function details()
    {
        return $this->hasMany(GoldPriceDetail::class);
    }

    // 🔥 Deskripsi otomatis untuk index
    public function getDescriptionAttribute()
    {
        $active = Carbon::parse($this->active_at)->format('d M Y');

        if ($this->expired_at) {
            $expired = Carbon::parse($this->expired_at)->format('d M Y');
            return "Harga emas tanggal {$active} - {$expired}";
        }

        return "Harga emas tanggal {$active}";
    }

    // 🔥 Status
    public function getStatusAttribute()
    {
        return $this->expired_at ? 'Expired' : 'Active';
    }
}
