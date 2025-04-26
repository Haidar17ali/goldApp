<?php

use App\Models\LPB;
use App\Models\PO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Log;
use App\Models\Stock;
use App\Models\LPBDetail;
use App\Models\PODetails;
use App\Models\User;
use App\Models\Notification;

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


// untuk lpb
function simpanDetailDanStock($lpbId, $detail, $poId){
    foreach (['afkir', '130', '260'] as $key) {
        $qty = (int) ($detail[$key] ?? 0);
        if ($qty > 0) {
            $length = $key === '260' ? 260 : 130;
            $quality = $key === 'afkir' ? 'Afkir' : 'Super';
            $productCode = $quality === 'Afkir'
                ? 'SGN.Af.130.' . $detail['diameter']
                : "SGN.Su.{$length}." . $detail['diameter'];

            // Cari harga dari PO detail
            $poDetail = PODetails::where('po_id', $poId)
                ->where('diameter_start', '<=', $detail['diameter'])
                ->where('diameter_to', '>=', $detail['diameter'])
                ->where('quality', $quality)
                ->where('length', (string)$length)
                ->first();

            // Update stock
            $log = Log::where('code', $productCode)->first();
            if ($log) {
                $stock = Stock::firstOrCreate(
                    ['log_id' => $log->id],
                    ['qty' => 0]
                );
                $stock->qty += $qty;
                $stock->save();
            }

            // Simpan LPB detail
            LPBDetail::create([
                'lpb_id' => $lpbId,
                'product_code' => $productCode,
                'length' => $length,
                'diameter' => $detail['diameter'],
                'qty' => $qty,
                'price' => $poDetail->price ?? 0,
                'quality' => $quality,
            ]);
        }
    }
}

function updateOrCreateStock($logId, $qty, $operation = 'tambah'){
    $stock = Stock::where('log_id', $logId)->first();

    if ($stock) {
        if ($operation === 'tambah') {
            $stock->qty += $qty;
        } elseif ($operation === 'kurangi') {
            $stock->qty -= $qty;
            if ($stock->qty < 0) $stock->qty = 0; // Hindari qty negatif
        }
        $stock->save();
    } else {
        // Jika belum ada dan operasinya tambah, buat baru
        if ($operation === 'tambah') {
            Stock::create([
                'log_id' => $logId,
                'qty' => $qty,
            ]);
        }
        // Jika belum ada dan operasinya kurangi, bisa diabaikan
    }
}

function updateLPBDetails($lpb, $newDetails, $poId)
{
    // 1. Kurangi stok dari detail lama dan hapus detail lama
    foreach ($lpb->details as $oldDetail) {
        $log = Log::where('code', $oldDetail->product_code)->first();
        if ($log) {
            $stock = Stock::where('log_id', $log->id)->first();
            if ($stock) {
                $stock->qty -= $oldDetail->qty;
                $stock->save();
            }
        }
    }
    // Hapus detail lama dan rollback stock (optional: bisa dikembangkan)
    LPBDetail::where('lpb_id', $lpb->id)->delete();

    // 2. Simpan detail baru dan tambahkan stok
    foreach ($newDetails as $detail) {
        foreach (['afkir', '130', '260'] as $key) {
            $qty = (int) ($detail[$key] ?? 0);
            if ($qty > 0) {
                $length = $key === '260' ? 260 : 130;
                $quality = $key === 'afkir' ? 'Afkir' : 'Super';
                $productCode = $quality === 'Afkir'
                    ? 'SGN.Af.130.' . $detail['diameter']
                    : "SGN.Su.{$length}." . $detail['diameter'];

                // Cari harga dari PO detail
                $poDetail = PODetails::where('po_id', $poId)
                    ->where('diameter_start', '<=', $detail['diameter'])
                    ->where('diameter_to', '>=', $detail['diameter'])
                    ->where('quality', $quality)
                    ->where('length', (string)$length)
                    ->first();

                // Update stock
                $log = Log::where('code', $productCode)->first();
                if ($log) {
                    $stock = Stock::firstOrCreate(
                        ['log_id' => $log->id],
                        ['qty' => 0]
                    );
                    $stock->qty += $qty;
                    $stock->save();
                }

                // Simpan LPB detail
                LPBDetail::create([
                    'lpb_id' => $lpb->id,
                    'product_code' => $productCode,
                    'length' => $length,
                    'diameter' => $detail['diameter'],
                    'qty' => $qty,
                    'price' => $poDetail->price ?? 0,
                    'quality' => $quality,
                ]);
            }
        }
    }
}

function sendToPermission($permission, $type, $title, $message, $link)
    {
        $users = User::permission($permission)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => false,
            ]);
        }
    }