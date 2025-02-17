<?php

function money_format($money){
    return number_format($money,0, ',','.');

}

function kubikasi($diameter,$length,$qty)
{
    return round($diameter*$diameter*$length*0.7854/1000000*$qty,4);
}