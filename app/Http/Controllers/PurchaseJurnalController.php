<?php

namespace App\Http\Controllers;

use App\Models\Down_payment;
use App\Models\LPB;
use App\Models\PurchaseJurnal;
use App\Models\PurchaseJurnalDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseJurnalController extends Controller
{
    public function index(){
        $purchase_jurnals = PurchaseJurnal::with(['details', 'createdBy'])->paginate(10);
        return view('pages.purchase-jurnals.index', compact(['purchase_jurnals']));
    }

    public function create(){
        $lpbs = LPB::where('approved_by', '!=', null)->where('status', 'Pending')->with(['roadPermit', 'supplier', 'details' => function ($query) {
            $query->orderBy('quality')->orderBy('diameter');
        }])->get();
        $down_payments = Down_payment::where('status', "Pending")->orwhere('status', "Sukses")->get();
        $redirectTo = session('lpb_edit_redirect', route('purchase-jurnal.buat')); // Ambil halaman asal dari session, default ke index lpb
        return view('pages.purchase-jurnals.create', compact(['lpbs', 'redirectTo']));
    }

    public function store(Request $request){
        $lpbs = $request->input('lpbs');
        $dp = $request->input('dp');
        $pj_code = "PJ". date('YmdHis').'.'.count($lpbs);

        $pj = PurchaseJurnal::create([
            'pj_code' => $pj_code,
            'date' => date('Y-m-d'),
            'created_by' => Auth::id(),
            'status' => 'Proses'
        ]);

        foreach($lpbs as $lpb){
            // update status lpb
            $dataLpb = LPB::where('id', $lpb['id'])->first();
            $dataLpb['status'] = 'Menunggu Pembayaran';
            $dataLpb->save();

            $pjDetail = [
                'pj_id' => $pj->id,
                'lpb_id' => $dataLpb->id,
                'status' => 'Pending',
            ];

            PurchaseJurnalDetail::create($pjDetail);
        }

        foreach ($dp as $index => $money) {
            $supplier = Supplier::where('id', $index)->first();
            if (!empty($supplier) && $money != 0) {
                $data = [
                    'supplier_id' => $supplier->id,
                    'pu_id' => $pj->id,
                    'nominal' => $money,
                    'date' => date('Y-m-d'),
                    'type' => 'Out',
                    'status' => 'Pending'
                ];
                Down_payment::create($data);
            }
        }

        return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);     
    }

    public function edit($id){
        // 
    }

    public function update(Request $request, $id){
        // 
    }

    public function destroy($id){
        // 
    }
}
