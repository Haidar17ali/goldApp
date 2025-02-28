<?php

namespace App\Http\Controllers;

use App\Models\PurchaseJurnal;
use Illuminate\Http\Request;

class PurchaseJurnalController extends Controller
{
    public function index(){
        $purchaseJurnals = PurchaseJurnal::with(['details'])->paginate(10);
        return view('pages.LPB.index', compact(['purchaseJurnals']));
    }

    public function create(){
        // 
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
