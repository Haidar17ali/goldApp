<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LPB;
use App\Models\RoadPermit;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LPBController extends Controller
{
    public function index(){
        $lpbs = LPB::with(['roadPermit', 'details', 'supplier', 'createdBy', 'editedBy', 'ApprovalBy'])->paginate(20);
        return view('pages.LPB.index', compact('lpbs'));
    }

    public function create(){
        $road_permits = RoadPermit::all();
        $suppliers = Supplier::where('supplier_type', 'Sengon')->get();
        $graders = Employee::where('position_id', 'Grader')->get();
        $tallies = Employee::where('position_id', 'Tally')->get();

        return view('pages.LPB.create', compact(['road_permit', 'suppliers', 'graders', 'tallies']));
    }

    public function store(Request $request){
        // 
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
