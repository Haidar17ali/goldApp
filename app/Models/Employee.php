<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'id',
        'nip',
        'pin',
        'nik',
        'no_kk',
        'fullname',
        'alias_name',
        'gender',
        'address_id',
        'employee_type',
        'position_id',
        'entry_date',
        'payment_type',
        'bank_id',
        'salary_id',
        'premi',
        'location',
        'jkn_number',
        'jkp_number',
        'mariage_status',
        'family_depents',
        'exit_date',
        'status',
    ];

    public function salary(){
        return $this->belongsTo(Salary::class, 'salary_id');
    }

    public function address(){
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
