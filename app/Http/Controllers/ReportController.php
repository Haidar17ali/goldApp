<?php

namespace App\Http\Controllers;

use App\Exports\AllLPBSupplierReportExport;
use App\Exports\LpbExportByNpwp;
use App\Exports\LpbReportExport;
use App\Exports\LPBSupplierReportExport;
use App\Models\Down_payment;
use App\Models\LPB;
use App\Models\npwp;
use App\Models\PO;
use App\Models\RoadPermit;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends BaseController
{
    public function reportRoadPermits(){
        $statuses = [
            'Menunggu Pembayaran',
            'Pengajuan Pembayaran',
            'Terbayar', 
            'Pending', 
            'Tolak'
        ];
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        return view('pages.Report.road-permits', compact(['suppliers', 'statuses']));
    }

    public function getRoadPermitReport(Request $request){
        $request->validate([
            'model' => 'required', // bisa diabaikan jika tidak pakai model dinamis
        ]);
    
        $start_date = $request->start_date;
        $last_date = $request->last_date;
        $nopol = $request->nopol;
        $supplier = $request->supplier;

        if($start_date == null && $last_date == null){
            $start_date = date('Y-m-d');
            $last_date = date('Y-m-d');
        }
    
        if ($last_date && !$start_date) {
            return response()->json(["status" => "no_start_date"]);
        }
    
        $query = Lpb::with('details')
            ->where('status', $request->status);
    
        if ($start_date && $last_date) {
            $query->whereBetween('date', [$start_date, $last_date]);
        } elseif ($start_date) {
            $query->whereDate('date', $start_date);
        }
    
        if ($nopol) {
            $query->where('nopol', 'LIKE', "%$nopol%");
        }

        if ($supplier) {
            $query->where('supplier_id', $supplier);
        }
    
        $lpbs = $query->orderBy('nopol', 'asc')->orderBy('no_kitir', 'asc')->get();
    
        // Grouping by nopol
        $grouped = $lpbs->groupBy('nopol');
    
        return response()->json([
            'table' => view('pages.Report.datas.lpb-pengajuan', compact('grouped'))->render()
        ]);
    }

    public function reportLPB(){
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        $statuses = [
            'Menunggu Pembayaran',
            'Pengajuan Pembayaran',
            'Terbayar', 
            'Pending', 
            'Tolak'
        ];
        return view('pages.Report.lpb', compact(['suppliers', 'statuses']));
    }

    public function getLpbReport(Request $request)
    {
        $query = Lpb::with(['details', 'supplier.bank', 'roadPermit', 'npwp']);
        $periode = null;

        $start_date = $request->start_date;
        $last_date = $request->last_date;
        $date_by = $request->dateBy ?? 'date1';
        
        if($start_date == null && $last_date == null){
            $start_date = date('Y-m-d');
            $last_date = date('Y-m-d');
        }

        $supplier = $request->supplier;
        $nopol = $request->nopol;
        $status = $request->status;
        $supplierDPRincian= [];

        // Filter tanggal (jika ada dan valid)
        if ($start_date && $last_date) {
            $periode = 'PERIODE ' . date('d/m/Y', strtotime($start_date)) . ' S.D ' . date('d/m/Y', strtotime($last_date));

            if ($date_by === 'date1') {
                $query->whereBetween('date', [$start_date, $last_date]);
            } elseif ($date_by === 'paid_at') {
                $query->whereBetween('paid_at', [$start_date, $last_date]);
            } elseif ($date_by === 'used_at') {
                $query->whereBetween('used_at', [$start_date, $last_date]);
            } elseif ($date_by === 'date2') {
                $query->whereHas('roadPermit', function ($q) use ($start_date, $last_date) {
                    $q->whereBetween('date', [$start_date, $last_date]);
                });
            }
            $down_payments = Down_payment::whereBetween('date', [$start_date, $last_date])
            ->select('supplier_id', DB::raw('SUM(nominal) as total_nominal'))
            ->groupBy('supplier_id')
            ->with('supplier') // jika ingin ambil nama supplier
            ->get();
        } elseif ($start_date) {
            $periode = 'PERIODE ' . date('d/m/Y', strtotime($start_date));

            if ($date_by === 'date1') {
                $query->whereDate('date', $start_date);
            } elseif ($date_by === 'paid_at') {
                $query->whereDate('paid_at', $start_date);
            } elseif ($date_by === 'used_at') {
                $query->whereDate('used_at', $start_date);
            } elseif ($date_by === 'date2') {
                $query->whereHas('roadPermit', function ($q) use ($start_date) {
                    $q->whereDate('date', $start_date);
                });
            }
            $down_payments = Down_payment::where('date', $start_date)
            ->select('supplier_id', DB::raw('SUM(nominal) as total_nominal'))
            ->groupBy('supplier_id')
            ->with('supplier') // jika ingin ambil nama supplier
            ->get();
        }

        // Filter supplier jika dikirim dan bukan default
        if ($supplier && $supplier != "Pilih Supplier") {
            $query->where('supplier_id', $supplier);
        }

        // Filter nopol jika dikirim
        if ($nopol) {
            $query->where('nopol', 'LIKE', "%$nopol%");
        }

        // Filter status jika dikirim dan bukan default
        if ($status && $status != "Pilih Status") {
            $query->where('status', $status);
        }

        $datas = $query->orderBy('nopol', 'asc')->orderBy('no_kitir', 'asc')->get();

        $dataUniqueLpbs = $query->orderBy('id', 'desc')->get()
                ->unique(function ($item) {
                    return $item->arrival_date . '-' . $item->supplier_id . '-' . $item->nopol;
                })
                ->values();
                
                $dpTotalsBySupplier = [];
                $processedDPIds = [];
                
            foreach ($dataUniqueLpbs as $lpb) {
                $supplierId = $lpb->supplier_id;
                $arrivalDate = $lpb->arrival_date;
                $nopol = $lpb->nopol;
            
            // Ambil semua DP (parent & child) berdasarkan supplier dan arrival_date
            $dpList = Down_payment::with(['details','children.details', 'parent.details'])
            ->where('supplier_id', $supplierId)
            ->whereHas("details", function($q) use ($nopol){
                $q->where("nopol", $nopol);
            })
            ->orWhere("arrival_date", $arrivalDate)
            ->where("parent_id", null)
            ->get();
            
            if(count($dpList) >= 1){
                // Ambil semua DP (parent & child) berdasarkan supplier dan arrival_date
                $dpList = Down_payment::with(['details','children.details', 'parent.details'])
                ->where('supplier_id', $supplierId)
                ->where("arrival_date", $arrivalDate)
                ->whereHas("details", function($q) use ($nopol){
                    $q->where("nopol", $nopol);
                })
                ->where("parent_id", null)
                ->get();
            }
            
            $total = 0;

            $supplierId = $lpb->supplier_id;

            // Inisialisasi array jika belum ada
            if (!isset($supplierDPRincian[$supplierId]) && count($dpList)>= 1) {
                $supplierDPRincian[$supplierId] = [
                    'supplier_name' => $lpb->supplier->name ?? 'Unknown',
                    'total' => 0,
                ];
            }

            $total = 0;
            $processedDPIds = [];

            foreach ($dpList as $dp) {
                if (in_array($dp->id, $processedDPIds)) {
                    continue;
                }

                $dpType = $dp->dp_type;
                $dpNominal = 0;
                $pelunasanNominal = 0;

                if ($dpType === 'DP') {
                    $dpNominal = $dp->nominal;
                    $pelunasanNominal = $dp->children->sum('nominal');

                    $processedDPIds[] = $dp->id;
                    foreach ($dp->children as $child) {
                        $processedDPIds[] = $child->id;
                    }
                } elseif ($dpType === 'Pelunasan' && $dp->parent_id === null) {
                    $pelunasanNominal = $dp->nominal;
                    $processedDPIds[] = $dp->id;
                } else {
                    continue;
                }

                $subTotal = $dpNominal + $pelunasanNominal;
                $total += $subTotal;
            }

            // Simpan total ke array utama jika ada nominalnya
            if ($total > 0) {
                $supplierDPRincian[$supplierId]['total'] += $total;
            }   
        }

        return response()->json([
            'table' => view('pages.Report.datas.data-lpb', compact(['datas', 'periode', 'supplierDPRincian', 'down_payments']))->render()
        ]);
    }

    public function reportLpbSupplier(){
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        return view('pages.Report.lpb-supplier', compact(['suppliers']));
    }

    public function getLpbSupplierReport(Request $request)
    {
        $data = getLPBSupplier($request);
        
        return response()->json([
            'table' => view('pages.Report.datas.data-lpb-supplier', [
                'groupedLpbs' => $data['groupedLpbs'],
                'grandTotal' => $data['grandTotal'],
                'periode' => $data['periode'],
                'start_date' => $data['start_date'],  // Kirim ke view
                'end_date' => $data['end_date'],  // Kirim ke view
                'dateBy' => $data['dateBy'],  // Kirim ke view
            ])->render()
        ]);
    }

    public function exportAllLpbSupplierExcel(Request $request)
    {
        $data = getLPBSupplier($request);
        
        return Excel::download(new AllLPBSupplierReportExport(
            $data['groupedLpbs'],
            $data['grandTotal'],
            $data['periode'],
            $data['start_date'],  // Kirim ke view
            $data['end_date'],  // Kirim ke view
            $data['dateBy'],  // Kirim ke view
        ), 'laporan-lpb-'.$request->start_date.'-'.$request->end_date.'-'.'.xlsx'); 
        
    }
    
    public function getLpbSupplierReportDetail(Request $request)
    {
        $data = getLPBSupplierDetail($request);

        return response()->json([
            'table' => view('pages.Report.datas.data-lpb-supplier', compact([
                'data',
            ]))->render()
        ]);
    }

    public function exportLpbSupplierPdf(Request $request)
    {
        $data = getLPBSupplierDetail($request);

        return Pdf::loadView('pages.Report.datas.pdf-lpb-supplier', $data)
            ->download('laporan-lpb-'.$request->start_date.'-'.$data['pemilik'].'-'.$data['nopolResult'].'.pdf');
    }
    
    public function exportLpbSupplierExcel(Request $request)
    {

        $data = getLPBSupplierDetail($request);

        return Excel::download(new LPBSupplierReportExport(
            $data['sortedResults'],
            $data['pemilik'],
            $data['tglKirim'],
            $data['nopolResult'],
            $data['grandTotalQty'],
            $data['grandTotalM3'],
            $data['grandTotalNilai'],
            $data['grandTotalPph'],
        ), 'laporan-lpb-'.$request->start_date.'-'.$data['pemilik'].'-'.$data['nopolResult'].'.xlsx');        
    }

    function reportDp() {
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        $statuses = [
            'DP',
            'Pelunasan',
            'Grade', 
            'Belum Grade',
        ];
        return view('pages.Report.dp', compact(['suppliers', 'statuses']));
    }

function getDpReport(Request $request)
{
    // Ambil semua supplier yang punya DP
    $dps = Down_Payment::with('supplier', "children", "parent", "details")
        ->where("parent_id", null)
        ->get()
        ->groupBy('supplier_id');

    $datas = [];
    
    foreach ($dps as $supplier_id => $dpGroup) {
        
        // Ambil kombinasi nopol & arrival_date dari DP
        $combinations = $dpGroup->map(function($dp) {
            // if($dp->parent_id == null){
                return [
                    'supplier_id' => $dp->supplier_id,
                    'nopol' => $dp->details?->first()->nopol??"-",
                    'arrival_date' => $dp->children?->first()->arrival_date??$dp->arrival_date,
                    'nominal' => $dp->nominal,
                    'pelunasan' => $dp->children?->first()->nominal??0,
                ];
                // }
            });
            
            // Cek semua LPB yang matching dengan DP (anggap sebagai pemakaian)
        $totalDP = 0;
        $usedDP = 0;
        foreach ($combinations as $index => $combo) {
            $totalDP += $combo['nominal'] + $combo['pelunasan'];

            $lpb = Lpb::where('supplier_id', $combo['supplier_id'])
                ->where('nopol', $combo['nopol'])
                ->whereDate('arrival_date', $combo['arrival_date'])
                ->where("paid_at", "!=", null)
                ->first();

            if ($lpb) {
                $usedDP += $combo['nominal'] + $combo['pelunasan'];
            }
        }


        $saldo = $totalDP - $usedDP;

        $datas[] = [
            'supplier_id' => $dpGroup->first()->supplier->id ?? 'Tanpa id',
            'supplier' => $dpGroup->first()->supplier->name ?? 'Tanpa Nama',
            'npwp' => $dpGroup->first()->supplier?->npwp->name ?? 'Tanpa Nama',
            'total_dp' => $totalDP,
            'used_dp' => $usedDP,
            'saldo' => $saldo,
        ];
    }

    return response()->json([
        'table' => view('pages.Report.datas.data-dp', [
            'datas' => $datas,
            // 'datas' => $filteredReport,
            'periode' => request('start_date') . ' - ' . request('end_date'),
            // 'down_payments' => $allDPs,
        ])->render()
    ]);
}

public function DPDetail(Request $request){
    $supplier_id = $request->supplier_id;
    $supplier = Supplier::where("id", $supplier_id)->first();
    $dps = Down_payment::with(['details', 'children', "parent"])->where("supplier_id", $supplier_id)->get();

    $mutasi = [];
    
    $allDpNominal =0;
    $allLpbNominal =0;
    if (count($dps)) {
        foreach($dps as $index => $dp){
            // hitung seluruh dp untuk mendapatkan saldo
            $allDpNominal += $dp->nominal;
            $nopolDP =$dp->parent?->details->first()?->nopol
            ?? $dp->details->first()?->nopol
            ?? "-";

            $indexMutasiMasuk = count($mutasi); // Simpan index sebelum push

            // ambil data dp taruk di mutasi
            $mutasi[] = [
                "tanggal" => $dp->date,
                "nopol" => $nopolDP,
                "jenis" => "Masuk",
                "keluar" => 0,
                "masuk" => $dp->nominal,
            ];
            
            $arrival_date = $dp->children->first()?->arrival_date??$dp->arrival_date;
            $nopol = $dp->details?->first()->nopol??"-";
    
            $dpTotal = $dp->nominal+$dp->children->first()?->nominal??0;
    
            // ambil semua lpbs
            $lpbs = LPB::with(['details'])->where("supplier_id", $supplier_id)->where("nopol", $nopol)
                ->whereDate('arrival_date', $arrival_date)->where("paid_at", "!=", null)->get();   
            
            $totalNilaiLpb = 0;
            $totalPotLpb = 0;
            $pph = 0;
            
            if(count($lpbs)){
            // Tambahkan <s> ke nopol mutasi Masuk yang bersangkutan
            $mutasi[$indexMutasiMasuk]["nopol"] = "<s>" . $nopolDP . "</s>";
                foreach($lpbs as $lpb){
                    // hitung Seluruh ptongan
                        $totalPotLpb += $lpb->conversion+$lpb->nota_conversion;
                        $valueLpb = 0;
                        // hitung nilai LPB nya
                        if(count($lpb->details)){
                            foreach($lpb->details as $detail){
                                $valueLpb = (kubikasi($detail->diameter,$detail->length,$detail->qty)*$detail->price);
                                $totalNilaiLpb += $valueLpb;
                            }
                        }
                        $totalNilaiLpb = $totalNilaiLpb+$lpb->conversion+$lpb->nota_conversion;
                        $pph = $totalNilaiLpb*0.0025;
                    }
                    $totalNilaiLpb = $dpTotal; //round($totalNilaiLpb)-round($pph);
                    $allLpbNominal += $totalNilaiLpb;
                    
                    $selisihDpLpb =$dpTotal-$totalNilaiLpb;

                    if($selisihDpLpb<1000){
                        
                        $mutasi[] = [
                            "tanggal" => $lpbs[0]->paid_at!= null?$lpbs[0]->paid_at: 0,
                            "nopol" => $lpbs[0]->nopol,
                            "jenis" => "Keluar",
                            "keluar" => $totalNilaiLpb,
                            "masuk" => 0,
                        ];
                    }
            }
            
        }
    }

    usort($mutasi, function($a, $b){
        return strtotime($a['tanggal']) <=> strtotime($b['tanggal']);
    });

    $saldoDP=0;
    $saldoLpb=0;
    foreach($mutasi as &$mut){
        $saldoDP += $mut["masuk"];
        $saldoLpb += $mut["keluar"];
        $mut["saldo"] = round($saldoDP)-round($saldoLpb); 
    }
    
    return view("pages.Report.datas.detail-dp", compact(["mutasi","saldoDP","saldoLpb", "supplier"]));
}

public function exportLpbByNpwp(Request $request)
{
    $query = Lpb::with(['details', 'supplier.bank', 'roadPermit', 'npwp', 'supplier']);

    // Tambahkan filter sesuai kebutuhan Anda seperti start_date, nopol, dll.
    $start_date = $request->start_date;
    $last_date = $request->last_date;
    $date_by = $request->dateBy ?? 'date1';
        
        if($start_date == null && $last_date == null){
            $start_date = date('Y-m-d');
            $last_date = date('Y-m-d');
        }

        $supplier = $request->supplier;
        $nopol = $request->nopol;
        $status = $request->status;

        // Filter tanggal (jika ada dan valid)
        if ($start_date && $last_date) {
            if ($date_by === 'date1') {
                $query->whereBetween('date', [$start_date, $last_date]);
            } elseif ($date_by === 'paid_at') {
                $query->whereBetween('paid_at', [$start_date, $last_date]);
            } elseif ($date_by === 'used_at') {
                $query->whereBetween('used_at', [$start_date, $last_date]);
            } elseif ($date_by === 'date2') {
                $query->whereHas('roadPermit', function ($q) use ($start_date, $last_date) {
                    $q->whereBetween('date', [$start_date, $last_date]);
                });
            }
        } elseif ($start_date) {
            if ($date_by === 'date1') {
                $query->whereDate('date', $start_date);
            } elseif ($date_by === 'paid_at') {
                $query->whereDate('paid_at', $start_date);
            } elseif ($date_by === 'used_at') {
                $query->whereDate('used_at', $start_date);
            } elseif ($date_by === 'date2') {
                $query->whereHas('roadPermit', function ($q) use ($start_date) {
                    $q->whereDate('date', $start_date);
                });
            }
        }

        // Filter supplier jika dikirim dan bukan default
        if ($supplier && $supplier != "Pilih Supplier") {
            $query->where('supplier_id', $supplier);
        }

        // Filter nopol jika dikirim
        if ($nopol) {
            $query->whereHas('roadPermit', function ($q) use ($nopol) {
                $q->where('nopol', 'LIKE', "%$nopol%");
            });
        }

        // Filter status jika dikirim dan bukan default
        if ($status && $status != "Pilih Status") {
            $query->where('status', $status);
        }
        
        $datas = $query->get();
    
    $grouped = $datas->groupBy(function ($item) {
        return $item->npwp->npwp ?? 'Tidak Ada NPWP';
    });

    $summary = collect();

    foreach ($grouped as $npwp => $items) {
        $first = $items->first();
        $namaLpb = $first->npwp->name ?? 'Tidak Diketahui'; // Ganti dengan field nama LPB Anda        
        $totalQty = 0;
        $totalKubikasi = 0;
        $totalNilaiLpb = 0;
        $totalKonversi = 0;
        $totalNotaKonversi = 0;
        $totalNilai = 0;
        $totalPph22 = 0;
        $totalGrandTotal = 0;
        
        foreach ($items as $lpb) {
            $lpbKubikasi = 0;
            $lpbNilaiLpb = 0;

            foreach ($lpb->details as $detail) {
                $qty = $detail->qty;
                $diameter = $detail->diameter;
                $length = $detail->length;
                $price = $detail->price;

                $kubikasi = kubikasi($diameter, $length, $qty);
                $nilaiLpb = $kubikasi * $price;

                $lpbKubikasi += $kubikasi;
                $lpbNilaiLpb += $nilaiLpb;

                $totalQty += $qty;
            }

            // $conversion = $lpb->conversion+$lpb->nota_conversion ?? 0;
            // $nilai = $lpbNilaiLpb + $lpb->conversion -abs($lpb->nota_conversion);
            $conversion = $lpb->conversion ?? 0;
            $nota_conversion = $lpb->nota_conversion ?? 0;
            $nilai = $lpbNilaiLpb + $lpb->conversion+$nota_conversion;
            $pph22 = $nilai * 0.0025;
            $grandTotal = $nilai - $pph22;
            
            $totalKubikasi += $lpbKubikasi;
            $totalNilaiLpb += $lpbNilaiLpb;
            $totalKonversi += $conversion + $nota_conversion;
            $totalNilai += $nilai;
            $totalPph22 += $pph22;
            $totalGrandTotal += $grandTotal;
        }
        
        $summary->push([
            'npwp' => $npwp,
            'nama_lpb' => $namaLpb,
            'total_qty' => $totalQty,
            'total_kubikasi' => $totalKubikasi,
            'nilai_lpb' => $totalNilaiLpb,
            'konversi' => $totalKonversi,
            'nilai' => $totalNilai,
            'pph22' => $totalPph22,
            'grand_total' => $totalGrandTotal,
        ]);
        
    }


    return Excel::download(new LpbExportByNpwp($summary), 'rekap_lpb_per_npwp.xlsx');
}



}
