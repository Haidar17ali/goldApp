<?php

use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function money_format($money)
{
    return number_format($money, 0, ',', '.');
}



function generateUniqueBarcode()
{
    do {
        $barcode = strtoupper(Str::random(6));
    } while (ProductVariant::where('barcode', $barcode)->exists());

    return $barcode;
}
