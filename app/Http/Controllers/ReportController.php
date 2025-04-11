<?php

namespace App\Http\Controllers;

use App\Models\LPB;
use App\Models\RoadPermit;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reportRoadPermits(){
        return view('pages.Report.road-permits');
    }

    public function getRoadPermitReport(Request $request){
        $request->validate([
            'model' => 'required',
        ]);
    
        $start_date = $request->start_date;
        $last_date = $request->last_date;
        $nopol = $request->nopol;
    
        if ($last_date && !$start_date) {
            return response()->json(["status" => "no_start_date"]);
        }
    
        $query = RoadPermit::query();
    
        if ($start_date && $last_date) {
            $query->whereBetween('date', [$start_date, $last_date]);
        } elseif ($start_date) {
            $query->whereDate('date', $start_date);
        }
    
        if ($nopol) {
            $query->where('nopol', 'LIKE', "%$nopol%");
        }
    
        $data = $query->orderBy('id', 'desc')->paginate(3);
    
        return response()->json([
            'table' => view('pages.search.search-RP', compact('data'))->render(),
            'pagination' => view('vendor.pagination.bootstrap-4', ['paginator' => $data])->render(),
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

    public function getLpbReport(Request $request){
        $start_date = $request->start_date;
        $last_date = $request->last_date;
        $supplier = $request->supplier;
        $nopol = $request->nopol;
        $status = $request->status;

        if ($last_date && !$start_date) {
            return response()->json(["status" => "no_start_date"]);
        }

        $query = Lpb::with(['details', 'supplier.bank', 'roadPermit', "npwp"]);

        if ($start_date && $last_date) {
            $query->whereBetween('date', [$start_date, $last_date]);
            $periode = 'PERIODE ' . date('d/m/Y', strtotime($start_date)) . ' S.D ' . date('d/m/Y', strtotime($last_date));
        } elseif ($start_date) {
            $periode = 'PERIODE ' . date('d/m/Y', strtotime($start_date));
            $query->whereDate('date', $start_date);
        }else{
            // Ambil tahun dari data pertama, atau dari hari ini kalau data kosong
            $anyDate = now();
            $periode = 'PERIODE TAHUN ' . date('Y', strtotime($anyDate));
        }

        if ($supplier) {
            $query->where('supplier_id', "$supplier");
        }

        if ($nopol) {
            $query->whereHas('roadPermit', function ($q) use ($nopol) {
                $q->where('nopol', 'LIKE', "%$nopol%");
            });
        }

        if ($status && $status != "Pilih Status") {
            $query->where('status', "$status");
        }else{
            $query->where('status', "Menunggu Pembayaran");
        }
        
        $datas = $query->orderBy('id', 'desc')->get(); // ubah sesuai kebutuhan
        
        return response()->json([
            'table' => view('pages.Report.datas.data-lpb', compact(['datas', 'periode']))->render()
        ]);
    }
}
