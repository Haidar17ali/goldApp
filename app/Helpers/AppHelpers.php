<?php

use App\Models\LPB;
use App\Models\PO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function money_format($money){
    return number_format($money,0, ',','.');

}

function kubikasi($diameter,$length,$qty)
{
    return round($diameter*$diameter*$length*0.7854/1000000*$qty,4);
}

function totalKubikasi($details){
    $total = 0;
    if (count($details)) {
        foreach($details as $detail){
            $total += kubikasi($detail->diameter, $detail->length, $detail->qty);
        }
    }
    return $total;
}

function nominalKubikasi($details){
    $total = 0;
    if (count($details)) {
        foreach($details as $detail){
            $total += kubikasi($detail->diameter, $detail->length, $detail->qty)*$detail->price;
            // dd($total);
        }
    }
    return $total;
}

function hitungTotalPembayaran($details)
    {
        $totalPembayaran = 0;
        
        foreach ($details as $detail) {
            $lpb = LPB::with('details')->find($detail->id);

            if ($lpb) {
                $totalLpb = 0;
                foreach ($lpb->details as $detail) {
                    $totalLpb += kubikasi($detail->diameter, $detail->length, $detail->qty) * $detail->price;
                }
                $totalPembayaran += $totalLpb;
            }
        }

        return "Rp.".money_format($totalPembayaran);
    }

function generateCode($prefix, $table, $dateColumn)
{
    $tanggal = Carbon::now()->format('Ymd'); // Format YYYYMMDD

    // Hitung jumlah record pada tanggal hari ini
    $count = DB::table($table)->whereDate($dateColumn, Carbon::today())->count();

    // Buat nomor urut dengan format 001, 002, dst.
    $noUrut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    // Gabungkan menjadi format {PREFIX}{YYYYMMDD}{XXX}
    return strtoupper($prefix) . $tanggal . $noUrut;
}