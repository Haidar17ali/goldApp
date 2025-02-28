<?php

use App\Models\PO;

function money_format($money){
    return number_format($money,0, ',','.');

}

function kubikasi($diameter,$length,$qty)
{
    return round($diameter*$diameter*$length*0.7854/1000000*$qty,4);
}

function nominalKubikasi($details){
    $total = 0;
    if (count($details)) {
        foreach($details as $detail){
            $total += kubikasi($detail->diameter, $detail->length, $detail->quantity)*$detail->price;
        }
    }
    return $total;
}