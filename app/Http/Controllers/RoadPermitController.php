<?php

namespace App\Http\Controllers;

use App\Models\RoadPermit;
use App\Models\RoadPermitDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;

class RoadPermitController extends Controller
{
    public function index($type){
        $road_permits = RoadPermit::orderBy('id', 'desc')->with(['details', 'handyman', 'createdBy', 'editedBy'])->get();
        return view('pages.road-permits.index', compact(['road_permits', 'type']));
    }

    public function create($type){
        $trucks = ['Pickup', 'Truk Engkel', 'Dump Truk', 'Truk Gandeng', 'Truk Fuso', 'Container'];
        $item_types = ['Sengon', 'Merbau', 'Pembantu'];
        return view('pages.road-permits.create', compact(['type', 'trucks', 'item_types']));
    }
    
    public function store(Request $request, $type){
        // Set time zone
        date_default_timezone_set('Asia/Jakarta');
        
        // Validasi road permit utama
        $validatedData = $request->validate([
            'from' => 'required|string',
            'destination' => 'required|string',
            'nopol' => 'required|string',
            'driver' => 'required|string',
        ]);

        // ambil data stringify yang dikirim fe dan decode menjadi json
        $details = json_decode($request->road_permit_details[0], true);

        // Validasi road permit details jika ada
        if(count($details)){
        
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                $validator = Validator::make($detail, [
                    'load' => 'required|string',
                    'amount' => 'required|numeric|min:1',
                    'unit' => 'required|string',
                ]);
        
                if ($validator->fails()) {
                    $detailErrors["details.$index"] = $validator->errors()->all();
                }
            }            
            
            if (!empty($detailErrors)) {
                return response()->json(['errors' => $detailErrors], 422);
            }
        }
    
        // Simpan data utama
        $data = [
            'code' => generateCode('SJ', 'road_permits', 'code'),
            'date' => date('Y-m-d'),
            'in' => date('H:i:s', time()),
            'from' => $request->from,
            'destination' => $request->destination,
            'vehicle' => $request->vehicle,
            'type_item' => $request->item_type,
            'nopol' => $request->nopol,
            'driver' => $request->driver,
            'handyman_id' => 1,
            'unpack_location' => $request->unpack_location,
            'sill_number' => $request->sill_number,
            'container_number' => $request->container_number,
            'description' => $request->description,
            'type' => $type,
            'status' => 'Proses Bongkar',
            'created_by' => Auth::user()->id,
        ];
    
        $permit = RoadPermit::create($data);

        if(count($details)){
            // Simpan detail data jika ada
            foreach ($details as $detail) {
                RoadPermitDetail::create([
                    'road_permit_id' => $permit->id,
                    'load' => $detail['load'],
                    'amount' => $detail['amount'],
                    'unit' => $detail['unit'],
                    'size' => $detail['size'] ?? null,
                    'cubication' => $detail['cubication'] ?? null,
                ]);
            }
        }
    
        session()->flash('status', 'saved');
        return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);        
    }

    public function showDetail($id)
    {
        $roadPermit = RoadPermit::with(['lpb.details'])->findOrFail($id);

        $groupedByKitir = $roadPermit->lpb->mapWithKeys(function ($lpb) {
            $details = $lpb->details;

            // Group by quality, then by category
            $groupedByQuality = $details->groupBy('quality')->map(function ($group) {
                return $group->groupBy('category');
            });

            return [$lpb->no_kitir => [
                'lpb' => $lpb,
                'grouped' => $groupedByQuality
            ]];
        });

        return view('pages.road-permits.modal-detail', compact('roadPermit', 'groupedByKitir'));
    }



    public function edit($id,$type){
        $road_permit = RoadPermit::with(['details'])->findOrFail($id);
        $trucks = ['Pickup', 'Truk Engkel', 'Dump Truk', 'Truk Gandeng', 'Truk Fuso', 'Container'];
        $item_types = ['Sengon', 'Merbau', 'Pembantu'];

        return view('pages.road-permits.edit', compact(['type', 'trucks', 'item_types', 'road_permit']));
    }

    public function update(Request $request, $id, $type){
        $road_permit = RoadPermit::with(['details'])->findOrFail($id);
        $road_permit->details()->delete();

        $details = json_decode($request->road_permit_details[0], true);
        // filter nilai null
        $filteredDetails = array_filter($details, function ($detail) {
            return !is_null($detail['load']) && !is_null($detail['amount']) && !is_null($detail['unit']);
        });

        $request->merge(['road_permit_details' => json_encode($filteredDetails)]);
        
        $details = json_decode($request->road_permit_details, true);
        
        // Validasi road permit utama
        $validatedData = $request->validate([
            'from' => 'required|string',
            'destination' => 'required|string',
            'nopol' => 'required|string',
            'driver' => 'required|string',
        ]);

        // ambil data stringify yang dikirim fe dan decode menjadi json

        // Validasi road permit details jika ada
        if(count($details)){   
            $detailErrors = [];
            foreach ($details as $index => $detail) {
                $validator = Validator::make($detail, [
                    'load' => 'required|string',
                    'amount' => 'required|numeric|min:1',
                    'unit' => 'required|string|in:Batang,Palet,Sak,Liter,Rit,Box,Pcs, Drum,Bandel,Box/Galon',
                ]);
        
                if ($validator->fails()) {
                    $detailErrors["details.$index"] = $validator->errors()->all();
                }
            }            
            
            if (!empty($detailErrors)) {
                return response()->json(['errors' => $detailErrors], 422);
            }
        }
    
        // Simpan data utama
            $road_permit->from = $request->from;
            $road_permit->destination = $request->destination;
            $road_permit->vehicle = $request->vehicle;
            $road_permit->type_item = $request->item_type;
            $road_permit->nopol = $request->nopol;
            $road_permit->driver = $request->driver;
            $road_permit->handyman_id = 1;
            $road_permit->unpack_location = $request->unpack_location;
            $road_permit->sill_number = $request->sill_number;
            $road_permit->container_number = $request->container_number;
            $road_permit->description = $request->description;
            $road_permit->type = $type;
            $road_permit->created_by = Auth::user()->id;
            $road_permit->save();
    
        // Simpan detail data jika ada
        if(count($details)){
            foreach ($details as $detail) {
                RoadPermitDetail::updateOrCreate(
                    [
                        'id' => $detail['id'] ?? null, // Cari berdasarkan id jika ada
                        'road_permit_id' => $road_permit->id, // Pastikan road_permit_id sesuai
                    ],
                    [
                        'load' => $detail['load'],
                        'amount' => $detail['amount'],
                        'unit' => $detail['unit'],
                        'size' => $detail['size'] ?? null,
                        'cubication' => $detail['cubication'] ?? null,
                    ]
                );
            }
        }
    
        session()->flash('status', 'edited');
        return response()->json(['message' => 'Surat jalan berhasil disimpan'], 200);
    }

    public function destroy($id){
        $road_permit = RoadPermit::with(['details'])->findOrFail($id);

        $road_permit->details()->delete();
        $road_permit->delete();
        return redirect()->back()->with('status', 'saved');
    }

    public function out($id){
        // Set time zone
        date_default_timezone_set('Asia/Jakarta');
        $road_permit = RoadPermit::findOrFail($id);

        $road_permit->out = date('Y-m-d H;i:s', time());
        $road_permit->status = 'Sudah Dibongkar';
        $road_permit->edited_by = Auth::user()->id;
        $road_permit->save();
        return redirect()->back()->with('status', 'edited');
    }

    public function setHandyman($id,$type){
        $road_permit = RoadPermit::findOrFail($id);

        return view('pages.road-permits.set-handyman', compact(['road_permit', 'type']));
    }
}
