<?php

namespace App\Http\Controllers;

use App\Models\RoadPermit;
use App\Models\RoadPermitDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoadPermitController extends Controller
{
    public function index($type){
        $road_permits = RoadPermit::orderBy('id', 'desc')->with(['details', 'handyman', 'createdBy', 'editedBy'])->get();
        return view('pages.road-permits.index', compact(['road_permits', 'type']));
    }

    public function create($type){
        $trucks = ['Pickup', 'Truk Engkel', 'Dump Truk', 'Truk Gandeng', 'Truk Fuso', 'Container'];
        return view('pages.road-permits.create', compact(['type', 'trucks']));
    }
    
    public function store(Request $request, $type){
        // set time zone
        date_default_timezone_set('Asia/Jakarta');

        try {
            // validasi
            $validator = Validator::make($request->all(), [
                'from' => 'required',
                'destination' => 'required',
                'nopol' => 'required',
                'driver' => 'required',
                'handyman' => 'required|exists:employees,id',
            ]);

            if($validator->fails()){
                return response()->json([
                    "status" => 'error',
                    "errors" => $validator->errors(),
                    "oldInput" => $request->all(),
                ], 422);
            }
        } catch (\Exception $e) {
            // Tangani exception dan kirim respon error yang lebih informatif
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() // Atau pesan error yang lebih umum
            ], 500); // Kode status 500 untuk Internal Server Error
        }

        
    }

    public function edit($id){
        // 
    }

    public function update(Request $request, $id){
        // 
    }

    public function destroy(){
        // 
    }
}
