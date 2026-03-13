<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        "code",
        "name",
        "category",
        "normal_balance",
        "parent_id",
        "is_active"
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // for lower case in db

    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value)
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value)
        );
    }

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class, 'chart_of_account_id');
    }
}
