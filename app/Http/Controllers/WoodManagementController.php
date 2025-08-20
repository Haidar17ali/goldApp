<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LPB;
use App\Models\WoodManagement;
use App\Models\WoodManagementDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WoodManagementController extends Controller
{
    public function index($type){
        return view("pages.productions.wood-managements.index", compact(['type']));
    }

    public function create($type){
        $rotariEmployees = Employee::whereHas('position', function ($query) {
            $query->where('name', 'Grader')->orWhere("name","Rotary");
        })->get();

        $lpbs = LPB::where('used', 1)
        ->whereDoesntHave('rotarySources')
        ->get();

        return view("pages.productions.wood-managements.create", compact(["type", "rotariEmployees", "lpbs"]));
    }

    public function store(Request $request, $type){
        DB::beginTransaction();

        try{
            $request->validate([
                "date" => "required|date",
                "grade" => "required",
                "no_kitir" => "required",
                "tally_id" => "exists:employees,id",
                "from" => "required|in:130,260",
                "to" => "required|in:130,260",
            ]);
            
            // Decode details dan filter yang valid
            $details = json_decode($request->details[0], true);
            // ambil data details yang lengkap saja
            $details = array_filter($details, function ($d) {
                return isset($d["no_kitir"]) && isset($d["source_diameter"])&& isset($d["source_qty"]) && isset($d["conversion_diameter"])&& isset($d["conversion_qty"]);
            });
            
            if (count($details) === 0) {
                return redirect()->back()->with('status', 'detailsNotFound');
            }
            
            
            if(count($details)){
                
                $detailErrors = [];
                foreach($details as $detail){
                    $validator = Validator::make([
                        "no_kitir"=>$detail["no_kitir"], 
                        "source_diameter"=>$detail["source_diameter"], 
                        "source_qty"=>$detail["source_qty"], 
                        "conversion_diameter"=>$detail["conversion_diameter"], 
                        "conversion_qty"=>$detail["conversion_qty"], 
                    ], [
                        "no_kitir" => 'nullable|numeric',
                        "source_diameter" => 'nullable|numeric',
                        "source_qty"=> 'nullable|numeric',
                        "conversion_diameter" => 'nullable|numeric',
                        "conversion_qty"=> 'nullable|numeric',
                    ]);
                }

                $wood_type = "Sengon";
                if($request->wood_type != null){
                    $wood_type = $request->wood_type;
                }

                if (!empty($detailErrors)) {
                    return response()->json(['errors' => $detailErrors], 422);
                }
                
                // insert data rotari
                $woodManagement = WoodManagement::create([
                    "date" =>$request->date,
                    "grade" => $request->grade,
                    "no_kitir" => $request->no_kitir,
                    "tally_id" => $request->tally_id,
                    "from" => $request->from,
                    "to" => $request->to,
                    "type" => $type,
                    "created_by"=> Auth::user()->id,
                ]);

                // simpan detail rotary
                
                foreach($details as $detail){
                    WoodManagementDetail::create([
                        "wood_management_id" => $woodManagement->id,
                        "lpb_id" => $detail["no_kitir"],
                        "source_diameter" => $detail["source_diameter"],
                        "source_qty" => $detail["source_qty"],
                        "conversion_diameter" => $detail["conversion_diameter"],
                        "conversion_qty" => $detail["conversion_qty"],
                    ]);
                }
            }

            DB::commit();
            session()->flash('status', 'saved');
            return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal simpan lpb',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id, $type){
        $woodManagement = WoodManagement::with(["details"])->findOrFail($id);
        $woodManagementId = $woodManagement->id;

        $rotariEmployees = Employee::whereHas('position', function ($query) {
            $query->where('name', 'Grader')->orWhere("name","Rotary");
        })->get();

        // ambil lpb yang sudah terpakai di lpb tapi belum ada di rotary
        $lpbs = LPB::where('used', 1)
        ->whereDoesntHave('rotarySources')
        ->get();

        $initialData = [];

        if(count($woodManagement->details)){
            foreach($woodManagement->details as $detail){
                $initialData[] = [
                    "no_kitir" => $detail->lpb_id,
                    "source_diameter" => $detail->source_diameter,
                    "source_qty" => $detail->source_qty,
                    "conversion_diameter" => $detail->conversion_diameter,
                    "conversion_qty" => $detail->conversion_qty,
                ];
            }
    
            $loopingData = 30-count($woodManagement->details);
            for($i =1 ; $i<=$loopingData; $i++){
                $initialData[] = [
                    "no_kitir" => 0,
                    "diameter" => 0,
                    "qty" => 0,
                ];
            }
            $initialData = json_encode($initialData);
        }

        return view("pages.productions.wood-managements.edit", compact(["woodManagement", "rotariEmployees", "lpbs", "initialData", "type"]));
    }

    public function update(Request $request, $id, $type){
        DB::beginTransaction();

        try{
            $woodManagement = WoodManagement::with(["details"])->findOrFail($id);
            $request->validate([
                "date" => "required|date",
                "grade" => "required",
                "no_kitir" => "required",
                "tally_id" => "exists:employees,id",
                "from" => "required|in:130,260",
                "to" => "required|in:130,260",
            ]);
            
            // Decode details dan filter yang valid
            $details = json_decode($request->details[0], true);
            // ambil data details yang lengkap saja
            $details = array_filter($details, function ($d) {
                return isset($d["no_kitir"]) && $d["no_kitir"] != 0 && isset($d["source_diameter"])&& isset($d["source_qty"]) && isset($d["conversion_diameter"])&& isset($d["conversion_qty"]);
            });
            
            if (count($details) === 0) {
                return redirect()->back()->with('status', 'detailsNotFound');
            }

            
            if(count($details)){                
                $detailErrors = [];
                foreach($details as $detail){
                    $validator = Validator::make([
                        "no_kitir"=>$detail["no_kitir"], 
                        "source_diameter"=>$detail["source_diameter"], 
                        "source_qty"=>$detail["source_qty"], 
                        "conversion_diameter"=>$detail["conversion_diameter"], 
                        "conversion_qty"=>$detail["conversion_qty"], 
                    ], [
                        "no_kitir" => 'nullable|numeric',
                        "source_diameter" => 'nullable|numeric',
                        "source_qty"=> 'nullable|numeric',
                        "conversion_diameter" => 'nullable|numeric',
                        "conversion_qty"=> 'nullable|numeric',
                    ]);
                }

                if (!empty($detailErrors)) {
                    return response()->json(['errors' => $detailErrors], 422);
                }
                
                // insert data rotari
                $woodManagement->date =$request->date;
                $woodManagement->grade = $request->grade;
                $woodManagement->no_kitir = $request->no_kitir;
                $woodManagement->tally_id = $request->tally_id;
                $woodManagement->from = $request->from;
                $woodManagement->to = $request->to;
                $woodManagement->type = $type;
                $woodManagement->created_by= Auth::user()->id;
                $woodManagement->save();

                // hapus data detail Lama
                if(count($woodManagement->details) > 0){
                    foreach($woodManagement->details as $woodDetail){
                        $woodDetail->delete();
                    }
                }

                // simpan detail rotary
                foreach($details as $detail){
                    WoodManagementDetail::create([
                        "wood_management_id" => $woodManagement->id,
                        "lpb_id" => $detail["no_kitir"],
                        "source_diameter" => $detail["source_diameter"],
                        "source_qty" => $detail["source_qty"],
                        "conversion_diameter" => $detail["conversion_diameter"],
                        "conversion_qty" => $detail["conversion_qty"],
                    ]);
                }
            }

            DB::commit();
            session()->flash('status', 'saved');
            return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);
        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal simpan lpb',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        $woodManagement = WoodManagement::with(["details"])->findOrFail($id);

        if(count($woodManagement->details)){
            $woodManagement->details()->delete();
        }
        $woodManagement->delete();
        return redirect()->back()->with(["status", "deleted"]);
    }
}
