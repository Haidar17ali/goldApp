<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PO;
use App\Models\PODetails;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class POController extends Controller
{
    public function index($type){
        $pos = PO::where('po_type', $type)->with(['createdBy', "edit_by"])->orderBy('id', 'desc')->paginate(20);
        return view('pages.PO.index', compact(['pos', 'type']));
    }

    public function create($type){
        $suppliers = Supplier::where('supplier_type', $type)->get();
        $supplier_types = ["Umum", "Khusus"];
        $employees = Employee::all();

        return view('pages.PO.create', compact(['type', 'suppliers', 'employees', "supplier_types"]));
    }

    public function store(Request $request, $type){
        // Set time zone
        date_default_timezone_set('Asia/Jakarta');
        $POCode = "PO.";
        if($type == "Sengon"){
            $request->validate([
                'supplier_id' => "required|exists:suppliers,id",
                'supplier_type' => "required|in:Umum,Khusus"
            ]);
            $POCode = "PO.". $type. ".". $request->supplier_type.".". date("ymdhis").".".Auth::id();
        }        
        
        // ambil data stringify yang dikirim fe dan decode menjadi json
        $details = json_decode($request->details[0], true);
        // filter nilai null
        $filteredDetails = array_filter($details, function ($detail) {
            return !is_null($detail['price']) && $detail['price'] != "";
        });

        $request->merge(['details' => json_encode($filteredDetails)]);
        
        $details = json_decode($request->details, true);

        // Validasi road permit details jika ada
        if(count($details)){
        
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                if($type == "Sengon"){
                    $validator = Validator::make($detail, [
                        'quality' => 'required|in:Super,Afkir',
                        'length' => 'required|numeric|in:130,260',
                        'diameter_start' => 'required|numeric',
                        'diameter_to' => 'required|numeric',
                        'price' => 'required|numeric',
                    ]);
                }
        
                if ($validator->fails()) {
                    $detailErrors["details.$index"] = $validator->errors()->all();
                }
            }            
            
            if (!empty($detailErrors)) {
                return response()->json(['errors' => $detailErrors], 422);
            }
        }else{
            return redirect()->back()->with('status', 'detailsNotFound');
        }
    
        // Simpan data utama
        $data = [
            'po_date' => date('Y-m-d'),
            'po_code' => $POCode,
            'po_type' => $type,
            'supplier_id' => $request->supplier_id,
            'supplier_type' => $request->supplier_type,
            'status' => 'Pending',
            'created_by' => Auth::user()->id,
        ];
    
        $PO = PO::create($data);
    
        // Simpan detail data jika ada
        foreach ($details as $detail) {
            PODetails::create([
                'po_id' => $PO->id,
                'quality' => $detail['quality'],
                'length' => strval($detail['length']),
                'diameter_start' => $detail['diameter_start'],
                'diameter_to' => $detail['diameter_to'],
                'price' => $detail['price'],
                'ppn' => $detail['ppn'] ?? null,
                'cubication' => $detail['cubication'] ?? null,
            ]);
        }
    
        session()->flash('status', 'saved');
        return response()->json(['message' => 'data berhasil disimpan'], 200);
    }

    public function edit($id, $type){
        $suppliers = Supplier::where('supplier_type', $type)->get();
        $po = PO::with(['details'])->findOrFail($id);
        $supplier_types = ["Umum", "Khusus"];
        $employees = Employee::all();
        return view('pages.PO.edit', compact(['po', 'type','suppliers', 'employees', 'supplier_types']));
    }

    public function update(Request $request, $id, $type){
        // Set time zone
        date_default_timezone_set('Asia/Jakarta');
        $po = PO::with(['details'])->findOrFail($id);
        $po->details()->delete();

        
        if($type == "Sengon"){
            $request->validate([
                'supplier_id' => "required|exists:suppliers,id",
                'supplier_type' => "required|in:Umum,Khusus"
            ]);
        }        
        
        // ambil data stringify yang dikirim fe dan decode menjadi json
        $details = json_decode($request->details[0], true);
        // filter nilai null
        $filteredDetails = array_filter($details, function ($detail) {
            return !is_null($detail['price']) && $detail['price'] != "";
        });

        $request->merge(['details' => json_encode($filteredDetails)]);
        
        $details = json_decode($request->details, true);

        // Validasi road permit details jika ada
        if(count($details)){
        
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                if($type == "Sengon"){
                    $validator = Validator::make($detail, [
                        'quality' => 'required|in:Super,Afkir',
                        'length' => 'required|numeric|in:130,260',
                        'diameter_start' => 'required|numeric',
                        'diameter_to' => 'required|numeric',
                        'price' => 'required|numeric',
                    ]);
                }
        
                if ($validator->fails()) {
                    $detailErrors["details.$index"] = $validator->errors()->all();
                }
            }            
            
            if (!empty($detailErrors)) {
                return response()->json(['errors' => $detailErrors], 422);
            }
        }else{
            return redirect()->back()->with('status', 'detailsNotFound');
        }
    
        // Simpan data utama
            $po->po_type = $type;
            $po->supplier_id = $request->supplier_id;
            $po->supplier_type = $request->supplier_type;
            $po->status = 'Pending';
            $po->edited_by = Auth::user()->id;
            $po->save();
    
        // Simpan detail data jika ada
        foreach ($details as $detail) {
            PODetails::create([
                'po_id' => $po->id,
                'quality' => $detail['quality'],
                'length' => strval($detail['length']),
                'diameter_start' => $detail['diameter_start'],
                'diameter_to' => $detail['diameter_to'],
                'price' => $detail['price'],
                'ppn' => $detail['ppn'] ?? null,
                'cubication' => $detail['cubication'] ?? null,
            ]);
        }
    
        session()->flash('status', 'edited');
        return response()->json(['message' => 'data berhasil disimpan'], 200);
    }

    public function destroy($id){
        $po = PO::with('details')->findOrFail($id);
        $po->details()->delete();
        $po->delete();
        return redirect()->back()->with('status', 'deleted');
    }
}
