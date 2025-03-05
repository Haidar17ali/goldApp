<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Log;
use App\Models\LPB;
use App\Models\LPBDetail;
use App\Models\npwp;
use App\Models\PO;
use App\Models\PODetails;
use App\Models\RoadPermit;
use App\Models\RoadPermitDetail;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LPBController extends Controller
{
    public function index(){
        $lpbs = LPB::with(['roadPermit', 'details', 'supplier', 'createdBy', 'editedBy', 'ApprovalBy'])->paginate(20);
        session(['lpb_edit_redirect' => route('lpb.index')]); // Simpan halaman asal di session
        return view('pages.LPB.index', compact('lpbs'));
    }

    public function create(){
        $road_permits = RoadPermit::where('type_item', 'Sengon')->where('status', 'Sudah dibongkar')->get();
        $purchase_orders = PO::where('po_type', 'Sengon')->where('status','Aktif')->get();
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
            'no_kitir' => 'required',
            'grader_id' => 'required',
            'tally_id' => 'required',
            'road_permit_id' => 'required|exists:road_permits,id',
            'supplier_id' => 'required',
            'npwp_id' => 'required',
            'nopol' => 'required|string',
            'po_id' => 'required|exists:p_o_s,id',
        ]);

        // ambil data stringify yang dikirim fe dan decode menjadi json
        $details = json_decode($request->details[0], true);
        $filteredData = array_filter($details, function ($detail) {
            return isset($detail['afkir']) || isset($detail[130]) || isset($detail[260]);
        });
        $details = $filteredData;

        // Validasi road permit details jika ada
        if(count($details)){        
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                isset($detail['afkir']) ? $detail['afkir'] = (int)$detail['afkir'] : 0;
                isset($detail['130']) ? $detail['130'] = (int)$detail['130'] : 0;
                isset($detail['260']) ? $detail['260'] = (int)$detail['260'] : 0;
                $validator = Validator::make($detail, [
                    'afkir' => 'nullable|numeric',
                    '130' => 'nullable|numeric',
                    '260' => 'nullable|numeric',
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
        $lpbCode = generateCode('LPB', 'l_p_b_s', 'lpb_date');
    
        // Simpan data utama
        $data = [
            'code' => $lpbCode,
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
            foreach (['afkir', '130', '260'] as $dataKey) {
                if (!empty($detail[$dataKey])) { // Hanya jika nilai tidak kosong
                    $productCode = "";
                    $length = ($dataKey == '260') ? 260 : 130;
                    $quality = ($dataKey == 'afkir') ? 'Afkir' : 'Super';
                    $qty = (int)$detail[$dataKey];
        
                    // Tentukan kode produk
                    if ($dataKey == '130') {
                        $productCode = 'SGN.Su.130.' . $detail['diameter'];
                    } elseif ($dataKey == '260') {
                        $productCode = 'SGN.Su.260.' . $detail['diameter'];
                    } else {
                        $productCode = 'SGN.Af.130.' . $detail['diameter'];
                    }

                    // Ambil data harga dari PO Detail berdasarkan diameter
                    $poDetail = PODetails::where('po_id', $request->po_id)
                    ->where('diameter_start', '<=', $detail['diameter'])
                    ->where('diameter_to', '>=', $detail['diameter'])
                    ->where('quality', $quality)
                    ->where('length', $length)
                    ->first();

                    // set data stock dan stock transaction
                    $log = Log::where('code', $productCode)->first();
                    // Update Stock dan Stock Transactions
                    if ($log) {
                        // Cek apakah stock sudah ada untuk log_id ini
                        $stock = Stock::where('log_id', $log->id)->first();

                        if ($stock) {
                            // Jika stock sudah ada, tambahkan qty
                            $stock->qty += $qty;
                            $stock->save();
                        } else {
                            // Jika stock belum ada, buat stock baru
                            Stock::create([
                                'log_id' => $log->id,
                                'qty' => $qty,
                            ]);
                        }
                    }
        
                    // Simpan ke database
                    LPBDetail::create([
                        'lpb_id' => $lpb->id,
                        'product_code' => $productCode,
                        'length' => $length,
                        'diameter' => $detail['diameter'],
                        'qty' => $qty,
                        'price' => $poDetail ? $poDetail->price : 0, // Hindari error jika PO detail tidak ditemukan
                        'quality' => $quality,
                    ]);
                }
            }
        }
        
        // Tambahkan transaksi stock
        StockTransaction::create([
            'lpb_id' => $lpb->id,
            'type' => 'Masuk', // Tipe masuk karena LPB menambah stock
        ]);

        $getRP = RoadPermit::where('id', $request->road_permit_id)->first();
        if($request->status_sj == "Selesai"){
            if($getRP){
                $getRP->status = $request->status_sj;
                $getRP->save();
            }
        }

    
        session()->flash('status', 'saved');
        return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);        
    }

    public function edit($id){
        $redirectTo = session('lpb_edit_redirect', route('lpb.index')); // Ambil halaman asal dari session, default ke index lpb
        $lpb = LPB::with(['details', 'roadPermit'])->findOrFail($id);

        // Buat array dengan diameter dari 8 sampai 65
        $diameters = range(8, 65);
        $data = [];

        // Siapkan struktur awal data
        foreach ($diameters as $diameter) {
            $data[$diameter] = [
                'diameter' => $diameter,
                'afkir' => 0,
                '130' => 0,
                '260' => 0,
                'kubikasi_afkir' => 0.0000,
                'kubikasi_130' => 0.0000,
                'kubikasi_260' => 0.0000,
                'total' => 0.0000
            ];
        }

        // proses convert data dari qty ke afkir,130,260
        if(count($lpb->details)){
            foreach($lpb->details as $detail){
                $diameter = $detail->diameter;
                if (isset($data[$diameter])) {
                    if ($detail->quality == 'Afkir' && $detail->length == 130) {
                        $data[$diameter]['afkir'] = $detail->qty;
                        $data[$diameter]['kubikasi_afkir'] = kubikasi($detail->diameter,$detail->length, $detail->qty);
                    } elseif ($detail->quality == 'Super' && $detail->length == 130) {
                        $data[$diameter]['130'] = $detail->qty;
                        $data[$diameter]['kubikasi_130'] = kubikasi($detail->diameter,$detail->length, $detail->qty);
                    } elseif ($detail->quality == 'Super' && $detail->length == 260) {
                        $data[$diameter]['260'] = $detail->qty;
                        $data[$diameter]['kubikasi_260'] = kubikasi($detail->diameter,$detail->length, $detail->qty);
                    }
                }
            }
        }
        // Ubah array ke format JSON untuk dikirim ke frontend
        $initialData = json_encode(array_values($data));

        $road_permits = RoadPermit::where('type_item', 'Sengon')->where('status', 'Sudah dibongkar')->orWhere('id', $lpb->road_permit_id) // Pastikan surat jalan yang sudah dipilih tetap muncul
        ->get();
        $purchase_orders = PO::where('po_type', 'Sengon')->where('status','Aktif')->get();
        $npwps = npwp::all();
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        $graders = Employee::where('position_id', 'Grader')->get();
        $tallies = Employee::where('position_id', 'Tally')->get();

        return view('pages.LPB.edit', compact(['road_permits', 'suppliers', 'graders', 'tallies', 'purchase_orders', 'npwps', 'lpb', 'initialData', 'redirectTo']));
    }

    public function update(Request $request, $id){
        $lpb = LPB::with(['details'])->findOrFail($id);
        // Set time zone
        date_default_timezone_set('Asia/Jakarta');
        
        // Validasi road permit utama
        $validatedData = $request->validate([
            'no_kitir' => 'required',
            'grader_id' => 'required',
            'tally_id' => 'required',
            'road_permit_id' => 'required|exists:road_permits,id',
            'supplier_id' => 'required',
            'npwp_id' => 'required',
            'nopol' => 'required|string',
            'po_id' => 'required|exists:p_o_s,id',
        ]);

        // ambil data stringify yang dikirim fe dan decode menjadi json
        $details = json_decode($request->details[0], true);

        $filteredData = array_filter($details, function ($detail) {
            return isset($detail['afkir']) || isset($detail[130]) || isset($detail[260]);
        });
        $details = $filteredData;

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
        $lpb->po_id = $request->po_id;
        $lpb->no_kitir = $request->no_kitir;
        $lpb->grader_id = $request->grader_id || 1;
        $lpb->tally_id = $request->tally_id || 1;
        $lpb->road_permit_id = $request->road_permit_id;
        $lpb->supplier_id = $request->supplier_id;
        $lpb->npwp_id = $request->npwp_id;
        $lpb->nopol = $request->nopol;
        $lpb->conversion = $request->conversion;
        $lpb->created_by = Auth::user()->id;
        // $lpb->save();

        // Ambil detail LPB lama
        $oldDetails = LPBDetail::where('lpb_id', $lpb->id)->get();
    
        // Simpan detail data jika ada
        foreach ($details as $key => $detail) {
            foreach (['afkir', '130', '260'] as $dataKey) {
                if (!empty($detail[$dataKey])) { // Hanya jika nilai tidak kosong
                    $productCode = "";
                    $length = ($dataKey == '260') ? 260 : 130;
                    $quality = ($dataKey == 'afkir') ? 'Afkir' : 'Super';
                    $qty = (int)$detail[$dataKey];
        
                    // Tentukan kode produk
                    if ($dataKey == '130') {
                        $productCode = 'SGN.Su.130.' . $detail['diameter'];
                    } elseif ($dataKey == '260') {
                        $productCode = 'SGN.Su.260.' . $detail['diameter'];
                    } else {
                        $productCode = 'SGN.Af.130.' . $detail['diameter'];
                    }

                    // Ambil data harga dari PO Detail berdasarkan diameter
                    $poDetail = PODetails::where('po_id', $request->po_id)
                    ->where('diameter_start', '<=', $detail['diameter'])
                    ->where('diameter_to', '>=', $detail['diameter'])
                    ->where('quality', $quality)
                    ->first();

                    $log = Log::where('code', $productCode)->first();
                    if($log){
                        // Hitung selisih qty
                        $oldQty = $oldDetails->where('product_code', $productCode)->sum('qty');
                        $qtyDiff = $qty - $oldQty;
                        
                        // Update Stock quantity
                        $stock = Stock::where('log_id', $log->id)->first();
                        if ($stock) {
                            $stock->qty += $qtyDiff;
                            $stock->save();
                        } else {
                            Stock::create([
                                'log_id' => $log->id,
                                'qty' => $qty,
                            ]);
                        }
                    }
        
                    // Simpan ke database
                    LPBDetail::create([
                        'lpb_id' => $lpb->id,
                        'product_code' => $productCode,
                        'length' => $length,
                        'diameter' => $detail['diameter'],
                        'qty' => $qty,
                        'price' => $poDetail ? $poDetail->price : 0, // Hindari error jika PO detail tidak ditemukan
                        'quality' => $quality,
                    ]);
                }
            }
        }

        // Dapatkan array ID dari koleksi $oldDetails
        $oldDetailIds = $oldDetails->pluck('id')->toArray();
        // Hapus detail LPB yang lama || Hapus semua detail LPB berdasarkan ID
        LPBDetail::whereIn('id', $oldDetailIds)->delete();
        // Hapus transaksi stock yang lama untuk LPB ini
        StockTransaction::where('lpb_id', $lpb->id)->delete();

        // Tambahkan transaksi stock
        StockTransaction::create([
            'lpb_id' => $lpb->id,
            'type' => 'Masuk', // Tipe masuk karena LPB menambah stock
        ]);
    
        session()->flash('status', 'edited');
        return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);
    }

    public function destroy($id){
        $lpb = LPB::with(['details'])->findOrFail($id);
        // Temukan LPB berdasarkan ID
        $lpb = LPB::find($id);

        if (!$lpb) {
            return response()->json(['message' => 'LPB not found'], 404);
        }

        // Ambil detail LPB
        $lpbDetails = LPBDetail::where('lpb_id', $lpb->id)->get();

        // Kurangi stok dan hapus transaksi stok
        foreach ($lpbDetails as $detail) {
            $log = Log::where('code', $detail->product_code)->first();
            if ($log) {
                $stock = Stock::where('log_id', $log->id)->first();
                if ($stock) {
                    $stock->qty -= $detail->qty;
                    $stock->save();
                }

                // Hapus transaksi stok
            }
        }
        
        // hapus stock transaksi
        StockTransaction::where('lpb_id', $lpb->id)->delete();
        // Hapus detail LPB
        $lpb->details()->delete();

        // Hapus LPB
        $lpb->delete();
            return redirect()->back()->with('status', 'deleted');
    }

}
