<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LPB;
use App\Models\LPBDetail;
use App\Models\npwp;
use App\Models\PO;
use App\Models\PODetails;
use App\Models\RoadPermit;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LPBController extends Controller
{
    public function index(){
        $lpbs = LPB::with(['roadPermit', 'details', 'supplier', 'createdBy', 'editedBy', 'ApprovalBy'])->paginate(20);
        return view('pages.LPB.index', compact('lpbs'));
    }

    public function create(){
        $road_permits = RoadPermit::where('type_item', 'Sengon')->get();
        $purchase_orders = PO::where('po_type', 'Sengon')->where('status->aktif')->get();
        $npwps = npwp::all();
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        $graders = Employee::where('position_id', 'Grader')->get();
        $tallies = Employee::where('position_id', 'Tally')->get();

        return view('pages.LPB.create', compact(['road_permits', 'suppliers', 'graders', 'tallies', 'purchase_orders', 'npwps']));
    }

    public function store(Request $request){
        // Set time zone
        date_default_timezone_set('Asia/Jakarta');
        
        // Validasi road permit utama
        $validatedData = $request->validate([
            // 'no_kitir' => 'required',
            // 'grader_id' => 'required',
            // 'tally_id' => 'required',
            // 'road_permit_id' => 'required|exists:road_permits,id',
            // 'supplier_id' => 'required',
            // 'npwp_id' => 'required',
            // 'nopol' => 'required|string',
            // 'po_id' => 'required|exists:p_o_s,id',
        ]);

        // ambil data stringify yang dikirim fe dan decode menjadi json
        $details = json_decode($request->details[0], true);

        // Validasi road permit details jika ada
        if(count($details)){
        
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                $detail['afkir'] = (int)$detail['afkir'];
                $detail['130'] = (int)$detail['130'];
                $detail['260'] = (int)$detail['260'];
                $validator = Validator::make($detail, [
                    'afkir' => 'numeric',
                    '130' => 'numeric',
                    '260' => 'numeric',
                ]);
        
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
            'lpb_date' => date('Y-m-d'),
            'po_id' => $request->po_id,
            'no_kitir' => $request->no_kitir,
            'grader_id' => $request->grader_id || 1,
            'tally_id' => $request->tally_id || 1,
            'road_permit_id' => $request->road_permit_id,
            'supplier_id' => $request->supplier_id,
            'npwp_id' => $request->npwp_id,
            'nopol' => $request->nopol,
            'conversion' => $request->conversion,
            'status' => 'Pending',
            'created_by' => Auth::user()->id,
        ];
    
        $lpb = LPB::create($data);
    
        // Simpan detail data jika ada
        foreach ($details as $key => $detail) {
            // make code
            foreach(array_keys($detail) as $dataKey){
                $productCode = "";
                $length = 130;
                $poDetail = null;
                $quality = 'Super';
                $qty = 0;
                if($dataKey == 130){
                    $poDetail = PODetails::where('po_id', $request->po_id)->where('diameter_start', '<=', $detail['diameter'])->where('diameter_to', '>=', $detail['diameter'])->first();
                    $qty = $detail[130];
                    $productCode = 'SGN.Su.130'.$detail['diameter'];
                }elseif($dataKey == 260){
                    $poDetail = PODetails::where('po_id', $request->po_id)->where('diameter_start', '<=', $detail['diameter'])->where('diameter_to', '>=', $detail['diameter'])->first();
                    $length = 260;
                    $qty = $detail[260];
                    $productCode = 'SGN.Su.260'.$detail['diameter'];
                }else{
                    $poDetail = PODetails::where('po_id', $request->po_id)->where('diameter_start', '<=', $detail['diameter'])->where('diameter_to', '>=', $detail['diameter'])->first();
                    $quality = 'Afkir';
                    $qty = $detail['afkir'];
                    $productCode = 'SGN.Af.130'.$detail['diameter'];
                }
                if($qty != ""){
                    dd([$detail, $qty]);
                    LPBDetail::create([
                        'lpb_id' => $lpb->id,
                        'product_code' => $productCode,
                        'length' => $length,
                        'diameter' => $detail['diameter'],
                        'qty' => $qty,
                        'price' => $poDetail->price,
                        'quality' => $quality,
                    ]);
                }
                dd("ok");
            }
        }
    
        session()->flash('status', 'saved');
        return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);        
    }

    public function edit($id){
        // 
    }

    public function update(){
        // 
    }

    public function destroy(){
        // 
    }
}
