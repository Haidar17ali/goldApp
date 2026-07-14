<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        "code",
        "name",
        "address"
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    public function transferOuts()
    {
        return $this->hasMany(TransferStock::class, 'from_branch_id');
    }

    public function transferIns()
    {
        return $this->hasMany(TransferStock::class, 'to_branch_id');
    }
}
