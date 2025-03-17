<?php

namespace App\Http\Controllers;

use App\Models\Down_payment;
use App\Models\LPB;
use App\Models\PurchaseJurnal;
use App\Models\PurchaseJurnalDetail;
use App\Models\PurchaseJurnalLpb;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseJurnalController extends Controller
{
    public function index(){
        $purchase_jurnals = PurchaseJurnal::with(['details.lpbs.details', 'createdBy'])->paginate(10);
        
        $purchase_jurnals->each(function ($purchaseJurnal) {
            $purchaseJurnal->allLpbs = $purchaseJurnal->details->flatMap(function ($detail) {
                return $detail->lpbs;
            });
            
            $failedLpbs = [];

            foreach ($purchaseJurnal->details as $detail) {
                foreach ($detail->lpbs as $lpb) {
                    if ($lpb->pivot->status === 'Gagal') {
                        $failedLpbs[] = $lpb;
                    }
                }
            }
            $purchaseJurnal->failedLpbs = $failedLpbs;
        });
        return view('pages.purchase-jurnals.index', compact(['purchase_jurnals']));
    }

    public function create(){
        $redirectTo = session('lpb_edit_redirect', route('purchase-jurnal.buat')); // Ambil halaman asal dari session, default ke index lpb
        return view('pages.purchase-jurnals.create', compact(['redirectTo']));
    }

    public function store(Request $request){
        DB::beginTransaction();

        try {
            // 1. Ambil data dari request
            $lpbs = $request->input('lpbs'); // Array data LPB yang mau diproses
            $dp = $request->input('dp'); // Array data DP
            $pj_code = "PJ" . date('YmdHis') . '.' . count($lpbs); // Buat kode unik
    
            // 2. Buat Purchase Jurnal (Header)
            $pj = PurchaseJurnal::create([
                'pj_code' => $pj_code,
                'date' => date('Y-m-d'),
                'created_by' => Auth::id(),
                'status' => 'Proses'
            ]);
            
            // 3. Buat Detail Purchase Jurnal
            $pjDetail = PurchaseJurnalDetail::create([
                'pj_id' => $pj->id,
                'status' => 'Pending', // Default status awal
            ]);
            
            // 4. Loop setiap LPB yang dipilih
            foreach ($lpbs as $lpb) {
                $lpbData = Lpb::with(['details'])->findOrFail($lpb['id']); // Ambil data LPB berdasarkan ID
                $pajak = nominalKubikasi($lpbData->details) * 0.0025;
                // 5. Simpan relasi LPB ke Detail lewat pivot
                $pjDetail->lpbs()->attach($lpbData->id, [
                    'pajak' => round($pajak), // Status awal LPB dalam jurnal ini
                    'status' => 'Pending', // Status awal LPB dalam jurnal ini
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                // 6. Update status global LPB jadi "Menunggu Pembayaran"
                $lpbData->update(['status' => 'Menunggu Pembayaran']);
            }
    
            // 7. Simpan data DP (Down Payment)
            foreach ($dp as $supplierId => $money) {
                $supplier = Supplier::find($supplierId); // Cek supplier
    
                if ($supplier && $money != 0) {
                    Down_payment::create([
                        'supplier_id' => $supplier->id,
                        'pu_id' => $pj->id,
                        'nominal' => $money,
                        'date' => date('Y-m-d'),
                        'type' => 'Out',
                        'status' => 'Pending'
                    ]);
                }
            }
    
            DB::commit(); // Commit transaksi jika semua berhasil
    
            return response()->json(['message' => 'Purchase jurnal berhasil disimpan!'], 200);
    
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback kalau ada error
            return response()->json(['message' => 'Gagal menyimpan data', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id){
        $pj = PurchaseJurnal::with([
            'details.lpbs.supplier',
            'details.lpbs.details' // untuk ambil detail kayu
        ])->findOrFail($id);
        
        // Ambil data LPB unik
        $lpbs = $pj->details->pluck('lpbs')->unique('id')->values(); 
        
        // Ambil hanya ID LPB dalam bentuk string
        $lpbs = $pj->details->pluck('lpbs')->unique('id')->values()[0];

        // untuk mendapatkan sisadp
        $lpbs->each(function ($lpb) {
            if ($lpb->supplier) {
                $lpb->supplier->sisaDp = $lpb->supplier->sisaDp();
            }
        });

        $lpbIds = $lpbs->pluck('id')->toArray();
        
        $redirectTo = session('lpb_edit_redirect', route('purchase-jurnal.buat')); // Ambil halaman asal dari session, default ke index lpb
        
        return view('pages.purchase-jurnals.edit', compact(['pj', 'lpbs', 'lpbIds', 'redirectTo']));
    }

    public function update(Request $request, $id){
        DB::beginTransaction();

        try {
            // 1. Ambil data dari request
            $lpbs = $request->input('lpbs'); // Array data LPB yang mau diproses
            $dp = $request->input('dp'); // Array data DP
            $deletedLpbs = $request->input('deleted_lpbs', []); // Array LPB yang mau dihapus

            // 2. Ambil Purchase Jurnal yang mau diupdate
            $pj = PurchaseJurnal::findOrFail($id);

            // 3. Update Purchase Jurnal (Header)
            $pj->update([
                'edited_by' => Auth::id(),
                'status' => 'Proses' // atau sesuaikan status yang diinginkan
            ]);

            // 4. Ambil Purchase Jurnal Detail yang terkait
            $pjDetail = PurchaseJurnalDetail::where('pj_id', $pj->id)->firstOrFail();

            // 5. Sync LPB yang dipilih
            $pjDetail->lpbs()->syncWithPivotValues(array_column($lpbs, 'id'), [
                'status' => 'Pending', // Atau sesuaikan status yang diinginkan
                'updated_at' => now()
            ]);

            // 6. Loop setiap LPB yang dipilih untuk update status global LPB
            foreach ($lpbs as $lpb) {
                $lpbData = Lpb::findOrFail($lpb['id']);
                $lpbData->update(['status' => 'Menunggu Pembayaran']);
            }
            
            // 7. Update status LPB yang dihapus
            foreach ($deletedLpbs as $lpbId) {
                $lpbData = Lpb::findOrFail($lpbId);
                $lpbData->update(['status' => 'Pending']); // Atau sesuaikan status yang diinginkan
            }

            // 8. Update atau buat DP (Down Payment)
            foreach ($dp as $supplierId => $money) {
                $supplier = Supplier::find($supplierId);

                if ($supplier && $money != 0) {
                    // Cek apakah DP sudah ada
                    $existingDp = Down_payment::where('supplier_id', $supplier->id)
                        ->where('pu_id', $pj->id)
                        ->first();

                    if ($existingDp) {
                        // Update DP yang sudah ada
                        $existingDp->update([
                            'nominal' => $money,
                            'date' => date('Y-m-d'),
                            'type' => 'Out',
                            'status' => 'Pending'
                        ]);
                    } else {
                        // Buat DP baru
                        Down_payment::create([
                            'supplier_id' => $supplier->id,
                            'pu_id' => $pj->id,
                            'nominal' => $money,
                            'date' => date('Y-m-d'),
                            'type' => 'Out',
                            'status' => 'Pending'
                        ]);
                    }
                }
            }

            DB::commit(); // Commit transaksi jika semua berhasil

            return response()->json(['message' => 'Purchase jurnal berhasil diupdate!'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback kalau ada error
            return response()->json(['message' => 'Gagal mengupdate data', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id){
        DB::beginTransaction();

        try {
            $purchaseJurnal = PurchaseJurnal::findOrFail($id);

            // 1. Ambil semua LPB yang terkait dengan Purchase Jurnal
            $lpbs = $purchaseJurnal->details->flatMap(function ($detail) {
                return $detail->lpbs;
            });

            // 2. Ubah status LPB menjadi "Pending"
            foreach ($lpbs as $lpb) {
                $lpb->update(['status' => 'Pending']);
            }

            // 3. Hapus Purchase Jurnal
            $purchaseJurnal->delete();

            DB::commit();

            return redirect()->back()->with('status', 'deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus purchase jurnal', 'error' => $e->getMessage()], 500);
        }
    }
}
