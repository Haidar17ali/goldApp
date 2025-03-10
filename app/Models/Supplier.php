<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'npwp_id',
        'nik',
        'supplier_type',
        'name',
        'phone',
        'address_id',
        'bank_id',
    ];

    
    public function address(){
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function npwp(){
        return $this->belongsTo(npwp::class, 'npwp_id');
    }

    public function dp(){
        return $this->hasMany(Down_payment::class);
    }

    // Menghitung Total DP Masuk
    public function totalDpMasuk()
    {
        return $this->dp()
            ->where('type', 'In')
            ->where('status', 'Sukses') // Sesuaikan dengan status yang menandakan transaksi berhasil
            ->sum('nominal');
    }

    // Menghitung Total DP Keluar
    public function totalDpKeluar()
    {
        return $this->dp()
            ->where('type', 'Out')
            ->where('status', 'Sukses') // Sesuaikan dengan status yang menandakan transaksi berhasil
            ->sum('nominal');
    }

    // Menghitung Sisa DP
    public function sisaDp()
    {
        return $this->totalDpMasuk() - $this->totalDpKeluar();
    }

}
