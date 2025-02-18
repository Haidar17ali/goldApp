<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PO;
use App\Models\Supplier;
use Illuminate\Http\Request;

class POController extends Controller
{
    public function index($type){
        $pos = PO::where('po_type', $type)->paginate(20);
        return view('pages.PO.index', compact(['pos', 'type']));
    }

    public function create($type){
        $suppliers = Supplier::where('supplier_type', $type)->get();
        $supplier_types = ["Umum", "Khusus"];
        $employees = Employee::all();

        return view('pages.PO.create', compact(['type', 'suppliers', 'employees', "supplier_types"]));
    }

    public function store(Request $request, $type){
        // 
    }

    public function edit($id, $type){
        // 
    }

    public function update(Request $request, $id, $type){
        // 
    }

    public function destroy($id){
        // 
    }
}
