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


}
