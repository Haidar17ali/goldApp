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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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

function generateCodeImport($prefix, $table, $dateColumn, $offset = 1)
{
     $tanggal = Carbon::now()->format('Ymd');
    $count = DB::table($table)->whereDate($dateColumn, Carbon::today())->count();
    
    while (true) {
        $count++;
        $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
        $code = strtoupper($prefix) . $tanggal . $noUrut;

        // Hindari error jika 'code' kolom tidak ada
        if (!Schema::hasColumn($table, 'code')) {
            throw new \Exception("Kolom 'code' tidak ditemukan di tabel {$table}");
        }

        // Periksa apakah code ini sudah ada
        $exists = DB::table($table)->where('code', '=', $code)->exists();

        if (!$exists) {
            return $code;
        }
    }
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

function getLPBSupplier(Request $request)
{
    $supplier = $request->supplier;
    $nopolInput = $request->nopol;
    $start_date = $request->start_date;
    $end = $request->last_date;
    $date_by = $request->dateBy ?: 'arrival_date';

    $periode = $start_date ? 'PERIODE ' . date('d/m/Y', strtotime($start_date)) : null;

    $query = Lpb::with(['details', 'supplier']);

    if ($start_date && $end) {
        $query->whereBetween($date_by, [$start_date, $end]);
    } elseif ($start_date) {
        $query->whereDate($date_by, $start_date);
    }

    if ($supplier && $supplier != 'Pilih Supplier') {
        $query->where('supplier_id', $supplier);
    }

    if ($nopolInput) {
        $query->where('nopol', 'LIKE', "%$nopolInput%");
    }

    $lpbs = $query->get();

    $groupedLpbs = [];
    $grandTotal = [
        'qty' => 0,
        'm3' => 0,
        'nilai' => 0,
        'pph' => 0,
        'transfer' => 0,
    ];

    foreach ($lpbs as $lpb) {
        $supplierName = $lpb->supplier->name ?? '-';
        $supplierId = $lpb->supplier->id ?? '-';
        $nopol = $lpb->nopol ?? '-';
        // Dinamis tanggal sesuai kolom yang dipilih user
        $tglKirim = '-';
        if (!empty($date_by) && !empty($lpb->$date_by)) {
            $tglKirim = $lpb->$date_by;
        }

        $groupKey = "{$nopol}_{$tglKirim}";

        // Siapkan grup supplier
        if (!isset($groupedLpbs[$supplierName])) {
            $groupedLpbs[$supplierName] = [];
        }

        // Siapkan grup berdasarkan nopol + tanggal
        if (!isset($groupedLpbs[$supplierName][$groupKey])) {
            $groupedLpbs[$supplierName][$groupKey] = [
                'kitir' =>$lpb->no_kitir ?? '-',
                'supplier' =>$lpb->supplier->name ?? '-',
                'npwp' =>$lpb->npwp->name ?? '-',
                'supplierId' =>$lpb->supplier->id ?? '-',
                'nopol' => $nopol,
                'tgl_kirim' => $tglKirim,
                'qty' => 0,
                'm3' => 0,
                'nilai' => 0,
                'pph' => 0,
                'transfer' => 0,
            ];
        }

        // Hitung total dari semua detail
        foreach ($lpb->details as $detail) {
            $detailQty = $detail->qty;
            $detailM3 = kubikasi((float)$detail->diameter, (float)$detail->length, $detailQty);
            $detailNilai = $detailM3 * $detail->price;

            $groupedLpbs[$supplierName][$groupKey]['qty'] += $detailQty;
            $groupedLpbs[$supplierName][$groupKey]['m3'] += $detailM3;
            $groupedLpbs[$supplierName][$groupKey]['nilai'] += $detailNilai;
        }
    }

    // Hitung PPh dan transfer sekaligus, serta grand total
    foreach ($groupedLpbs as $supplierName => &$groups) {
        foreach ($groups as &$row) {
            $row['pph'] = $row['nilai'] * 0.0025;
            $row['transfer'] = $row['nilai'] - $row['pph'];

            $grandTotal['qty'] += $row['qty'];
            $grandTotal['m3'] += $row['m3'];
            $grandTotal['nilai'] += $row['nilai'];
            $grandTotal['pph'] += $row['pph'];
            $grandTotal['transfer'] += $row['transfer'];
        }
    }

    // set header view date
    $dateByLabelMap = [
        'arrival_date' => 'TGL KIRIM',
        'date' => 'TGL LPB',
        'paid_at' => 'TGL BAYAR',
        // Tambahkan jika ada pilihan tanggal lain
    ];

    $headerDate = $dateByLabelMap[$date_by] ?? strtoupper(str_replace('_', ' ', $date_by));

    return [
        'groupedLpbs' => $groupedLpbs,
        'grandTotal' => $grandTotal,
        'periode' => $periode,
        'headerDate' => $headerDate,  // ini yang baru
        'dateBy' => $date_by,          // opsional, jika ingin pakai di view
        'start_date' => $start_date,
        'end_date' => $end,
    ];        // opsional, jika ingin pakai di view);
}


function getLPBSupplierDetail(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    $date_by = $request->date_by;
    $supplier = $request->supplier;
    $nopol = $request->nopol;

    $periode = ($start_date && $end_date)
        ? 'PERIODE ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date))
        : ($start_date ? 'PERIODE ' . date('d/m/Y', strtotime($start_date)) : null);

    $query = Lpb::with(['details', 'roadPermit', 'supplier', 'PO']);

    if ($start_date && $end_date) {
        $query->whereBetween($date_by, [$start_date, $end_date]);
    } elseif ($start_date) {
        $query->whereDate($date_by, $start_date);
    }
    
    if ($supplier && $supplier != 'Pilih Supplier') {
        $query->where('supplier_id', $supplier);
    }
    
    if ($nopol) {
        $query->where('nopol', 'LIKE', "%$nopol%");
    }

    $lpbs = $query->get();

    $firstLpb = $lpbs->first();
    $nopolResult = $firstLpb->nopol ?? '-';
    $pemilik = $firstLpb?->supplier?->name ?? '-';
    $po = $firstLpb->PO;

    $details = $lpbs->pluck('details')->flatten();

    // Grouping berdasarkan kualitas|panjang|diameter
    $grouped = $details->groupBy(function ($item) {
        return "{$item->quality}|{$item->length}|{$item->diameter}";
    });

    $results = [];

    foreach ($grouped as $key => $items) {
        [$quality, $length, $diameter] = explode('|', $key);
        $qty = $items->sum('qty');
        $m3 = kubikasi((float)$diameter, (float)$length, $qty);
        $price = $items->first()->price ?? 0;

        // proses export BAKUL
        if($request->type == "BKL"){
            // ambil harga bakul
            $bakulPrice = PO::orderBy('id', 'desc')
                    ->where('activation_date', $po->activation_date)->where('description', "Bakul")->first();
            if($bakulPrice){
                // Cari harga dari PO detail
                $poDetail = PODetails::where('po_id', $bakulPrice->id)
                    ->where('diameter_start', '<=', $diameter)
                    ->where('diameter_to', '>=', $diameter)
                    ->where('quality', $quality)
                    ->where('length', (string)$length)
                    ->first();
                $price = $poDetail->price ??0;
            }
        }
        $nilai = $m3 * $price;

        $keyLabel = strtolower($quality . ' ' . $length);
        $results[$keyLabel][$length][] = [
            'diameter' => $diameter,
            'harga' => $price,
            'qty' => $qty,
            'm3' => $m3,
            'nilai' => $nilai,
        ];
    }

    // Urutkan berdasarkan urutan kualitas tertentu
    $orderedKeys = ['afkir 130', 'super 130', 'super 260'];
    $sortedResults = [];

    foreach ($orderedKeys as $key) {
        if (isset($results[$key])) {
            $sortedResults[$key] = $results[$key];
        }
    }

    // Tambahkan sisa kualitas lain jika ada
    foreach ($results as $key => $value) {
        if (!isset($sortedResults[$key])) {
            $sortedResults[$key] = $value;
        }
    }

    // Grand Total
    $grandTotalQty = $details->sum('qty');
    // dd($grandTotalQty);
    $grandTotalM3 = $details->sum(function ($item) {
        return kubikasi((float)$item->diameter, (float)$item->length, $item->qty);
    });
    $grandTotalNilai = $details->sum(function ($item) use($request, $po) {
        // ambil harga bakul
        $price =0;
        $BP = PO::orderBy('id', 'desc')
                ->where('activation_date', $po->activation_date)->where('description', "Bakul")->first();
        if($BP){
            // Cari harga dari PO detail
            $poDetail = PODetails::where('po_id', $BP->id)
                ->where('diameter_start', '<=', $item->diameter)
                ->where('diameter_to', '>=', $item->diameter)
                ->where('quality', $item->quality)
                ->where('length', (string)$item->length)
                ->first();
                $price = $poDetail->price ??0;
            }
            // end ambil harga po bakul
            $harga = $request->type == "BKL" ? (float)$price : (float)$item->price;
        return kubikasi((float)$item->diameter, (float)$item->length, $item->qty) * $harga;
    });

    // Grand Total PPh22 (dari semua LPB)
    // Hitung total PPh22 manual
    $grandTotalPph = 0;

    foreach ($lpbs as $lpb) {
        $lpbTotal = 0;

        // ambil harga bakul
        $BP = PO::orderBy('id', 'desc')
                ->with(["details"])
                ->where('activation_date', $po->activation_date)->where('description', "Bakul")->first();
        if($BP){
            // Cari harga dari PO detail
            $poDetail = PODetails::where('po_id', $BP->id)
                ->where('diameter_start', '<=', $diameter)
                ->where('diameter_to', '>=', $diameter)
                ->where('quality', $quality)
                ->where('length', (string)$length)
                ->first();
            $price = $poDetail->price ??0;
        }
        // end ambil harga po bakul

        foreach ($lpb->details as $detail) {
            $m3 = kubikasi((float)$detail->diameter, (float)$detail->length, $detail->qty);
            $harga = $request->type == "BKL" ? (float) $price : (float) $detail->price;
            $nilai = $m3 * $harga;
            $lpbTotal += $nilai;
        }
        

        $pph = $lpbTotal * 0.0025;
        $grandTotalPph += $pph;
    }

    return compact(
        'po',
        'sortedResults',
        'periode',
        'nopolResult',
        'pemilik',
        'grandTotalQty',
        'grandTotalM3',
        'grandTotalNilai',
        'grandTotalPph'
    );
}
