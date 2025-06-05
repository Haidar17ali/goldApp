<?php

namespace App\Http\Controllers;

use App\Exports\AllLPBSupplierReportExport;
use App\Exports\LpbReportExport;
use App\Exports\LPBSupplierReportExport;
use App\Models\LPB;
use App\Models\RoadPermit;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends BaseController
{
    public function reportRoadPermits(){
        return view('pages.Report.road-permits');
    }

    public function getRoadPermitReport(Request $request){
        $request->validate([
            'model' => 'required', // bisa diabaikan jika tidak pakai model dinamis
        ]);
    
        $start_date = $request->start_date;
        $last_date = $request->last_date;
        $nopol = $request->nopol;
    
        if ($last_date && !$start_date) {
            return response()->json(["status" => "no_start_date"]);
        }
    
        $query = Lpb::with('details')
            ->where('status', 'menunggu pembayaran');
    
        if ($start_date && $last_date) {
            $query->whereBetween('date', [$start_date, $last_date]);
        } elseif ($start_date) {
            $query->whereDate('date', $start_date);
        }
    
        if ($nopol) {
            $query->where('nopol', 'LIKE', "%$nopol%");
        }
    
        $lpbs = $query->orderBy('date', 'asc')->get();
    
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

        $supplier = $request->supplier;
        $nopol = $request->nopol;
        $status = $request->status;

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

        $datas = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'table' => view('pages.Report.datas.data-lpb', compact(['datas', 'periode']))->render()
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
                'headerDate' => $data['headerDate'],  // Kirim ke view
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
            $data['headerDate'],  // Kirim ke view
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
            $data['periode'],
            $data['nopolResult'],
            $data['grandTotalQty'],
            $data['grandTotalM3'],
            $data['grandTotalNilai'],
            $data['grandTotalPph']
        ), 'laporan-lpb-'.$request->start_date.'-'.$data['pemilik'].'-'.$data['nopolResult'].'.xlsx');        
    }

}
