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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LPBController extends BaseController
{
    public function index(){
        $lpbs = LPB::with(['roadPermit', 'details', 'supplier', 'createdBy', 'editedBy', 'ApprovalBy'])->paginate(20);
        session(['lpb_edit_redirect' => route('lpb.index')]); // Simpan halaman asal di session
        return view('pages.LPB.index', compact('lpbs'));
    }

    public function create(){
        $road_permits = RoadPermit::where('type_item', 'Sengon')->where('status', 'Sudah dibongkar')->get();
        $purchase_orders = PO::with(['supplier'])->where('po_type', 'Sengon')->where('status','Aktif')->get();
        $npwps = npwp::all();
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        $graders = Employee::where('position_id', 'Grader')->get();
        $tallies = Employee::where('position_id', 'Tally')->get();

        return view('pages.LPB.create', compact(['road_permits', 'suppliers', 'graders', 'tallies', 'purchase_orders', 'npwps']));
    }

    public function store(Request $request){
        DB::beginTransaction();
    
        try {
            date_default_timezone_set('Asia/Jakarta');
    
            // Set status berdasarkan checkbox perhutani
            $status = $request->perhutani ? "Terbayar" : "Pending";
            $perhutani = $request->perhutani ? true : false;
    
            // Validasi input utama
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
    
            // Decode details dan filter yang valid
            $details = json_decode($request->details[0], true);
            $details = array_filter($details, function ($d) {
                return isset($d['afkir']) || isset($d[130]) || isset($d[260]);
            });
    
            if (count($details) === 0) {
                return redirect()->back()->with('status', 'detailsNotFound');
            }
    
            // Validasi isi detail
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                $validator = Validator::make([
                    'afkir' => $detail['afkir'] ?? 0,
                    '130' => $detail['130'] ?? 0,
                    '260' => $detail['260'] ?? 0,
                ], [
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
    
            // Generate LPB Code
            $lpbCode = generateCode('LPB', 'l_p_b_s', 'date');
    
            // Simpan LPB utama
            $lpb = LPB::create([
                'code' => $lpbCode,
                'date' => date('Y-m-d'),
                'po_id' => $request->po_id,
                'no_kitir' => $request->no_kitir,
                'grader_id' => $request->grader_id ?? 1,
                'tally_id' => $request->tally_id ?? 1,
                'road_permit_id' => $request->road_permit_id,
                'supplier_id' => $request->supplier_id,
                'npwp_id' => $request->npwp_id,
                'nopol' => $request->nopol,
                'conversion' => $request->conversion,
                'perhutani' => $perhutani,
                'status' => $status,
                'created_by' => Auth::user()->id,
            ]);
    
            // Simpan detail dan update stock
            foreach ($details as $detail) {
                simpanDetailDanStock($lpb->id, $detail, $request->po_id);
            }
    
            // Transaksi stock (global, bukan per item)
            StockTransaction::create([
                'lpb_id' => $lpb->id,
                'type' => 'Masuk',
            ]);
    
            // Update status road permit kalau diminta selesai
            if ($request->status_sj === "Selesai") {
                RoadPermit::where('id', $request->road_permit_id)->update([
                    'status' => 'Selesai'
                ]);
            }
    
            DB::commit();
            session()->flash('status', 'saved');
            return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);
    
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal simpan lpb',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function edit($id){
        $redirectTo = session('lpb_edit_redirect', route('lpb.index')); // Ambil halaman asal dari session, default ke index lpb
        $lpb = LPB::with(['details', 'roadPermit'])->findOrFail($id);
        
        if ($lpb->is_approved && auth()->user()->hasRole('admin')) {
            abort(403, 'LPB yang sudah disetujui tidak dapat diedit oleh role Anda.');
        }
        
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

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Set time zone
            date_default_timezone_set('Asia/Jakarta');

            $status = "Pending";
            $perhutani = false;
            if ($request->perhutani == true) {
                $status = "Terbayar";
                $perhutani = $request->perhutani;
            }

            // Validasi input utama
            $validatedData = $request->validate([
                'no_kitir' => 'required',
                'road_permit_id' => 'required|exists:road_permits,id',
                'supplier_id' => 'required',
                'npwp_id' => 'required',
                'nopol' => 'required|string',
                'po_id' => 'required|exists:p_o_s,id',
            ]);

            $details = json_decode($request->details[0], true);
            $filteredData = array_filter($details, function ($detail) {
                return isset($detail['afkir']) || isset($detail[130]) || isset($detail[260]);
            });

            if (count($filteredData) === 0) {
                return redirect()->back()->with('status', 'detailsNotFound');
            }

            $detailErrors = [];
            foreach ($filteredData as $index => $detail) {
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

            // Update data utama LPB
            $lpb = LPB::findOrFail($id);
            $lpb->update([
                'po_id' => $request->po_id,
                'no_kitir' => $request->no_kitir,
                'grader_id' => $request->grader_id ?? 1,
                'tally_id' => $request->tally_id ?? 1,
                'road_permit_id' => $request->road_permit_id,
                'supplier_id' => $request->supplier_id,
                'npwp_id' => $request->npwp_id,
                'nopol' => $request->nopol,
                'conversion' => $request->conversion,
                'perhutani' => $perhutani,
                'status' => $status,
                'updated_by' => Auth::user()->id,
            ]);

            // Tambahkan detail dan stock baru pakai helper
            
            // Dengan helper updateLPBDetails
            updateLPBDetails($lpb, $filteredData, $request->po_id);


            // Update stock transaction
            $stockTransaction = StockTransaction::firstOrNew(['lpb_id' => $lpb->id]);
            $stockTransaction->type = 'Masuk';
            $stockTransaction->save();

            // Update status road permit jika selesai
            $getRP = RoadPermit::find($request->road_permit_id);
            if ($request->status_sj === "Selesai" && $getRP) {
                $getRP->status = "Selesai";
                $getRP->save();
            }

            DB::commit();
            session()->flash('status', 'updated');
            return response()->json(['message' => 'Surat jalan berhasil diperbarui'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update LPB', 'error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $lpb = LPB::with('details')->findOrFail($id);

            // Kurangi stok sesuai dengan detail
            foreach ($lpb->details as $detail) {
                $log = Log::where('code', $detail->product_code)->first();
                if ($log) {
                    updateOrCreateStock($log->id, $detail->qty, 'kurangi');
                }
            }

            // Hapus transaksi stok
            StockTransaction::where('lpb_id', $lpb->id)->delete();

            // Hapus semua detail LPB
            $lpb->details()->delete();

            // Hapus LPB utama
            $lpb->delete();

            DB::commit();
            return redirect()->back()->with('status', 'deleted');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus LPB', 'error' => $e->getMessage()], 500);
        }
    }

    private function consumeLPB($id)
    {
        $lpb = LPB::with('details')->findOrFail($id);
    
        if ($lpb->used) {
            throw new \Exception("LPB sudah digunakan.");
        }
    
        foreach ($lpb->details as $detail) {
            $log = Log::where('code', $detail->product_code)->first();
            if ($log) {
                updateOrCreateStock($log->id, $detail->qty, 'kurangi');
            }
        }
    
        $lpb->used = true;
        $lpb->used_at = now();
        $lpb->save();
    
        StockTransaction::create([
            'lpb_id' => $lpb->id,
            'type' => 'Keluar',
        ]);
    }

    public function used($id){
        DB::beginTransaction();
    
        try{
            $this->consumeLPB($id);
            DB::commit();
            return redirect()->back()->with('status', "used");
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menggunakan lpb', 'error' => $e->getMessage()], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'selected' => 'required|array',
                'status' => 'required|string',
            ]);

            $selectedIds = $request->selected;
            $targetStatus = $request->status;

            $allowedStatuses = ['Terbayar', 'Ditolak', 'Setujui', 'Terpakai'];
            if (!in_array($targetStatus, $allowedStatuses)) {
                return response()->json(['message' => 'Status tidak valid.'], 400);
            }            

            $lpbs = LPB::whereIn('id', $selectedIds)->get();
    
            if($request->status != "Terpakai" && $request->status != "Setujui"){
                // Validasi untuk "Terbayar"
                if ($targetStatus == "Terbayar") {
                    $invalid = $lpbs->filter(fn($lpb) => $lpb->status != "Menunggu Pembayaran");
                    if ($invalid->isNotEmpty()) {
                        return response()->json([
                            'message' => 'Gagal: Semua status harus Menunggu Pembayaran untuk dijadikan Terbayar.'
                        ], 400);
                    }

                    // Update ke Terbayar
                    DB::table('l_p_b_s')->whereIn('id', $selectedIds)->update([
                        'paid_at' => now(),
                        'status' => "Terbayar"
                    ]);
                }

                // Validasi untuk "Ditolak"
                elseif ($targetStatus == "Ditolak") {
                    $invalid = $lpbs->filter(fn($lpb) => $lpb->status != "Pending");
                    if ($invalid->isNotEmpty()) {
                        return response()->json([
                            'message' => 'Gagal: Hanya status Pending yang dapat ditolak.'
                        ], 400);
                    }

                    // Update ke Ditolak
                    DB::table('l_p_b_s')->whereIn('id', $selectedIds)->update([
                        'status' => "Ditolak"
                    ]);
                }
            }elseif($request->status == "Setujui"){
                DB::table('l_p_b_s')->whereIn('id', $request->selected)->update([
                    'status' => "Menunggu Pembayaran",
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }elseif ($request->status == "Terpakai") {
                foreach($request->selected as $selectedId){
                    $this->consumeLPB($selectedId);
                }
            }
    
            DB::commit();
            return response()->json(['message' => 'Status berhasil diperbarui!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

}
