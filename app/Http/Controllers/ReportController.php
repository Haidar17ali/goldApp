<?php

namespace App\Http\Controllers;

use App\Models\RoadPermit;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reportRoadPermits(){
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        return view('pages.Report.road-permits', compact('suppliers'));
    }

    public function getRoadPermitReport(Request $request){
        $request->validate([
            'model' => 'required',
            // 'start_date' => 'date',
            // 'last_date' => 'date',
        ]);
        
        $start_date = $request->start_date;
        $last_date = $request->last_date;
        $nopol = $request->nopol;

        $data = RoadPermit::orderBy('id', "desc")->paginate(3);
        if($start_date != ""){
            $data = RoadPermit::orderBy('id', "desc")->where('date', $start_date)->paginate(3);
            
            if($last_date != ""){
                $data = RoadPermit::orderBy('id', "desc")->whereBetween('date', [$start_date, $last_date])->paginate(3);
            }
            if($last_date != "" && $nopol != ""){
                $data = RoadPermit::orderBy('id', "desc")->whereBetween('date', [$start_date, $last_date])->orWhere("nopol", "%$nopol%")->paginate(3);
            }
        }elseif($nopol != ""){
            $data = RoadPermit::orderBy('id', "desc")->where('nopol', "LIKE", "%$nopol%")->paginate(3);
        }elseif($last_date != "" && $start_date == ""){
            return response()->json(["status" => "no_start_date"]);
        }
        
        return response()->json([
            'table' => view('pages.search.search-RP', compact(['data']))->render(),
            'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
        ]);
    }
}
