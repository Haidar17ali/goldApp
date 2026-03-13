<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nik',
        'nama',
        'alamat',
        'status',
        'no_hp',
        'no_rek',
        'nama_bank',
        'gaji',
        'branch_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
