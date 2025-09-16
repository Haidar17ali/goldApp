<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

function money_format($money){
    return number_format($money,0, ',','.');

}
