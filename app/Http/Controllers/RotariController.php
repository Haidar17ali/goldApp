<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LPB;
use App\Models\Rotary;
use App\Models\RotaryDetail;
use App\Models\WoodManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RotariController extends Controller
{
    public function index($type){
        return view("pages.productions.rotary/index", compact("type"));
    }

    public function create($type){

        $lpbs = LPB::where('used', 1)
            ->whereDoesntHave('rotarySources', function ($q) {
                $q->where('source_type', LPB::class);
            })
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'no_kitir' => $item->no_kitir,
                'source_type' => LPB::class
            ]);

        // Wood
        $woods = WoodManagement::where("grade", "!=", "Afkir")->whereDoesntHave('rotarySources', function ($q) {
                $q->where('source_type', WoodManagement::class);
            })
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'no_kitir' => $item->no_kitir,
                'source_type' => WoodManagement::class
            ]);

        if($type == "Bensaw"){
            $woods = WoodManagement::where('type', 'Afkir')
            ->orwhere("type", "Downgrade")
            ->where('to', "130")
            ->where('grade', 'afkir')
            ->whereDoesntHave('rotarySources', function ($q) {
                $q->where('source_type', WoodManagement::class);
            })
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'no_kitir' => $item->no_kitir,
                'source_type' => WoodManagement::class
            ]);
        }

        $kitirs = $lpbs->merge($woods);

        
        $rotariEmployees = Employee::whereHas('position', function ($query) {
            $query->where('name', 'Grader')->orWhere("name","Rotary");
        })->get();
        return view("pages.productions.rotary.create", compact(["kitirs", "rotariEmployees", "type"]));
    }

    public function store(Request $request, $type){
        DB::beginTransaction();

        try{
            $request->validate([
                "date" => "required|date",
                "shift" => "required",
                "tally_id" => "exists:employees,id",
                "kitirs.*" => "required"
            ]);

            // Decode details dan filter yang valid
            $details = json_decode($request->details[0], true);
            // ambil data details yang lengkap saja
            $details = array_filter($details, function ($d) {
                return isset($d["no_kitir"]) && isset($d["height"])&& isset($d["length"])&& isset($d["width"])&& isset($d["qty"])&& isset($d["grade"]);
            });

            if (count($details) === 0) {
                return redirect()->back()->with('status', 'detailsNotFound');
            }

            
            if(count($details)){
                
                $detailErrors = [];
                foreach($details as $detail){
                    $validator = Validator::make([
                        "no_kitir"=>$detail["no_kitir"], 
                        "cubication"=>$detail["cubication"], 
                        "height"=>$detail["height"], 
                        "width"=>$detail["width"], 
                        "length"=>$detail["length"], 
                        "qty"=>$detail["qty"], 
                        "grade"=>$detail["grade"], 
                    ], [
                        "no_kitir" => 'nullable|numeric',
                        "cubication" => 'nullable|numeric',
                        "height"=> 'nullable|numeric',
                        "width"=> 'nullable|numeric',
                        "length"=> 'nullable|numeric',
                        "qty"=> 'nullable|numeric',
                        "grade" => 'nullable|in:OPC,PPC',
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
                $rotari = Rotary::create([
                    "date" =>$request->date,
                    "shift" => $request->shift,
                    "type" => $type,
                    "wood_type" => $wood_type,
                    "tally_id" => $request->tally_id,
                    "created_by"=> Auth::user()->id,
                ]);

                // simpan detail rotary
                foreach($details as $detail){
                    RotaryDetail::create([
                        "rotary_id" => $rotari->id,
                        "no_kitir" => $detail["no_kitir"],
                        "height" => $detail["height"],
                        "width" => $detail["width"], 
                        "length" => $detail["length"] ,
                        "qty" => $detail["qty"],
                        "grade" => $detail["grade"],
                    ]);
                }
            }

            if (count($request->kitirs)) {
                foreach($request->kitirs as $kitir){
                    [$sourceType, $sourceId] = explode('|', $kitir);
                     $rotari->rotariSources()->create([
                        "rotary_id" => $rotari->id,
                        'source_id' => $sourceId,
                        'source_type' => $sourceType,
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
        $rotari = Rotary::with(["details", "rotariSources"])->findOrFail($id);
        $initialData = [];
        
        if(count($rotari->details)){
            foreach($rotari->details as $detail){
                $initialData[] = [
                    "no_kitir" => $detail->no_kitir,
                    "height" => $detail->height,
                    "width" => $detail->width,
                    "length" => $detail->length,
                    "qty" => $detail->qty,
                    "grade" => $detail->grade,
                ];
            }
    
            $loopingData = 15-count($rotari->details);
            for($i =1 ; $i<=$loopingData; $i++){
                $initialData[] = [
                    "no_kitir" => 0,
                    "height" => 0,
                    "width" => 0,
                    "length" => 0,
                    "qty" => 0,
                    "grade" => 0,
                ];
            }
            $initialData = json_encode($initialData);
        }

        // Ambil ID kitir yang sudah dipakai di rotary ini
        $currentKitirIds = $rotari->rotariSources->map(fn($rs) => [
            'id' => $rs->source_id,
            'type' => $rs->source_type
        ]);

        // LPB
        $lpbs = LPB::where('used', 1)
            ->where(function ($q) use ($currentKitirIds) {
                $q->whereDoesntHave('rotarySources', function ($q2) {
                    $q2->where('source_type', LPB::class);
                })
                ->orWhereIn('id', $currentKitirIds
                    ->where('type', LPB::class)
                    ->pluck('id'));
            })
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'no_kitir' => $item->no_kitir,
                'source_type' => LPB::class
            ]);

        // Wood
        $woods = WoodManagement::where("type", "!=", "Afkir")
            ->where(function ($q) use ($currentKitirIds) {
                $q->whereDoesntHave('rotarySources', function ($q2) {
                    $q2->where('source_type', WoodManagement::class);
                })
                ->orWhereIn('id', $currentKitirIds
                    ->where('type', WoodManagement::class)
                    ->pluck('id'));
            })
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'no_kitir' => $item->no_kitir,
                'source_type' => WoodManagement::class
            ]);

        // Kalau type Bensaw
        if ($type == "Bensaw") {
            $woods = WoodManagement::where('type', 'bensaw')
                ->where('grade', 'afkir')
                ->where(function ($q) use ($currentKitirIds) {
                    $q->whereDoesntHave('rotarySources', function ($q2) {
                        $q2->where('source_type', WoodManagement::class);
                    })
                    ->orWhereIn('id', $currentKitirIds
                        ->where('type', WoodManagement::class)
                        ->pluck('id'));
                })
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id,
                    'no_kitir' => $item->no_kitir,
                    'source_type' => WoodManagement::class
                ]);
        }

        $kitirs = $lpbs->merge($woods);

        $selectedSources = $rotari->rotariSources()
            ->get()
            ->map(fn($src) => $src->source_type . '|' . $src->source_id)
            ->toArray();

        $rotariEmployees = Employee::whereHas('position', function ($query) {
            $query->where('name', 'Grader')->orWhere("name","Rotary");
        })->get();

        return view("pages.productions.rotary.edit", compact(["kitirs", "rotariEmployees", "type", "rotari", "selectedSources", "initialData"]));
    }

    public function update(Request $request, $id, $type){
        DB::beginTransaction();

        try{
            $rotari = Rotary::with(["details", "rotariSources"])->findOrFail($id);

            $request->validate([
                "date" => "required|date",
                "shift" => "required",
                "tally_id" => "exists:employees,id",
                "kitirs.*" => "required"
            ]);

            // Decode details dan filter yang valid
            $details = json_decode($request->details[0], true);
            // ambil data details yang lengkap saja
            $details = array_filter($details, function ($d) {
                return isset($d["no_kitir"]) && isset($d["height"])&& isset($d["length"])&& isset($d["width"])&& isset($d["qty"])&& isset($d["grade"]);
            });

            if (count($details) === 0) {
                return redirect()->back()->with('status', 'detailsNotFound');
            }

            if(count($details)){
                
                $detailErrors = [];
                foreach($details as $detail){
                    $validator = Validator::make([
                        "no_kitir"=>$detail["no_kitir"], 
                        "height"=>$detail["height"], 
                        "width"=>$detail["width"], 
                        "length"=>$detail["length"], 
                        "qty"=>$detail["qty"], 
                        "grade"=>$detail["grade"], 
                    ], [
                        "no_kitir" => 'nullable|numeric',
                        "height"=> 'nullable|numeric',
                        "width"=> 'nullable|numeric',
                        "length"=> 'nullable|numeric',
                        "qty"=> 'nullable|numeric',
                        "grade" => 'nullable|in:OPC,PPC',
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
                    $rotari->date = $request->date;
                    $rotari->shift =  $request->shift;
                    $rotari->type =  $type;
                    $rotari->wood_type =  $wood_type;
                    $rotari->tally_id =  $request->tally_id;
                    $rotari->edited_by=  Auth::user()->id;
                    $rotari->save();

                // hapus detail Rotari terlebih dahuru
                foreach($rotari->details as $detail){
                    $detail->delete();
                }

                // simpan detail rotary
                foreach($details as $detail){
                    RotaryDetail::create([
                        "rotary_id" => $rotari->id,
                        "no_kitir" => $detail["no_kitir"],
                        "height" => $detail["height"],
                        "width" => $detail["width"], 
                        "length" => $detail["length"] ,
                        "qty" => $detail["qty"],
                        "grade" => $detail["grade"],
                    ]);
                }
            }
            
            if (count($request->kitirs)) {
                foreach($request->kitirs as $kitir){
                    [$sourceType, $sourceId] = explode('|', $kitir);
                     $rotari->rotariSources()->create([
                        "rotary_id" => $rotari->id,
                        'source_id' => $sourceId,
                        'source_type' => $sourceType,
                    ]);
                }
            }

            DB::commit();
            session()->flash('status', 'updated');
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
        DB::beginTransaction();

        try{
            $rotari = Rotary::with(['details', 'rotariSources'])->findOrFail($id);

            if(count($rotari->details)){
                foreach($rotari->details as $detail){
                    $detail->delete();
                }
            }

            if(count($rotari->rotariSources)){
                foreach($rotari->rotariSources as $rotariLpb){
                    $rotariLpb->delete();
                }
            }

            $rotari->delete();
            DB::commit();
            return redirect()->back()->with('status', 'deleted');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data rotari', 'error' => $e->getMessage()], 500);
        }
        
    }
}
