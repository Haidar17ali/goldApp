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
        'position_id',
        'entry_date',
        'salary_type',
        'bank_name',
        'bank_account',
        'number_account',
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
}
