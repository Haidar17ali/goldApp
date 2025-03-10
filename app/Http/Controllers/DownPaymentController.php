<?php

namespace App\Http\Controllers;

use App\Models\Down_payment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DownPaymentController extends Controller
{
    public function index() {
        $down_payments = Down_payment::orderBy('id', 'desc')->get();
        return view('pages.down-payments.index', compact(['down_payments']));        
    }

    public function create(){
        $suppliers = Supplier::all();
        return view('pages.down-payments.create', compact(['suppliers']));
    }

    public function store(Request $request){
        $this->validate($request, [
            'supplier' => 'required|exists:suppliers,id',
            'nominal' => 'required|numeric',
        ]);

        $data = [
            'supplier_id' => $request->supplier,
            'nominal' => $request->nominal,
            'date' => date('Y-m-d'),
            'type' => 'In',
            'status' => 'Pending'
        ];

        Down_payment::create($data);
        return redirect()->route('down-payment.index')->with('status', 'saved');
    }

    public function edit($id){
        $down_payment = Down_payment::findOrFail($id);
        $suppliers = Supplier::all();
        return view('pages.down-payments.edit', compact(['suppliers', 'down_payment']));        
    }

    public function update(Request $request, $id){
        $down_payment = Down_payment::findOrFail($id);
        
        $this->validate($request, [
            'supplier' => 'required|exists:suppliers,id',
            'nominal' => 'required|numeric',
        ]);

        
        $down_payment->supplier_id = $request->supplier;
        $down_payment->nominal = $request->nominal;        
        $down_payment->save();
        return redirect()->route('down-payment.index')->with('status', 'edited');
    }

    public function destroy($id){
        Down_payment::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'deleted');
    }
}
