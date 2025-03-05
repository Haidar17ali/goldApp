<?php

namespace App\Http\Controllers;

use App\Models\LPB;
use App\Models\PurchaseJurnal;
use Illuminate\Http\Request;

class PurchaseJurnalController extends Controller
{
    public function index(){
        $purchase_jurnals = PurchaseJurnal::with(['details'])->paginate(10);
        return view('pages.purchase-jurnals.index', compact(['purchase_jurnals']));
    }

    public function create(){
        $lpbs = LPB::where('approved_by', '!=', null)->with(['roadPermit', 'supplier', 'details' => function ($query) {
            $query->orderBy('quality')->orderBy('diameter');
        }])->get();
        $redirectTo = session('lpb_edit_redirect', route('purchase-jurnal.buat')); // Ambil halaman asal dari session, default ke index lpb
        return view('pages.purchase-jurnals.create', compact(['lpbs', 'redirectTo']));
    }

    public function store(Request $request){
        // 
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
