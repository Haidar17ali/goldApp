<?php

namespace App\Http\Controllers;

use App\Models\Down_payment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownPaymentController extends BaseController
{
    public function index($type) {
        return view('pages.down-payments.index',compact('type'));        
    }

    public function create($type){
        $suppliers = Supplier::all();
        $dp_types = [
            "DP",
            "Pelunasan"
        ];
        $down_payments = Down_payment::with('supplier')
        ->where('dp_type', 'DP')
        ->whereDoesntHave('children')
        ->get();
        $detail_inputs = [
            ['length' => 130, 'quality' => 'super'],
            ['length' => 260, 'quality' => 'super'],
        ];
        return view('pages.down-payments.create', compact(['suppliers', 'type', 'dp_types', 'down_payments', 'detail_inputs']));
    }

    public function store(Request $request, $type){
        DB::beginTransaction();

        try{
            $this->validate($request, [
            'date' => 'required|date',
            'nota_date' => 'required|date',
            'supplier' => 'required|exists:suppliers,id',
            'nominal' => 'required|numeric',
            'nopol' => 'required',
            'dp_type' => 'required|in:DP,Pelunasan',
            ]);

            if($request->dp_type == "Pelunasan"){
                $this->validate($request, [
                'arrival_date' => 'required|date',
                ]);
                Down_payment::where("id", $request->dp_id)->update([
                    "arrival_date" => $request->arrival_date
                ]);
            }

            $data = [
                'date' => $request->date,
                'nota_date' => $request->nota_date,
                'supplier_id' => $request->supplier,
                'arrival_date' => $request->arrival_date,
                'nominal' => $request->nominal,
                'type' => $type,
                'dp_type' => $request->dp_type,
                'parent_id' => $request->dp_id,
            ];
            
            $dp = Down_payment::create($data);

            // Loop data detail
            if($request->dp_id == null){
                foreach ($request->length as $i => $length) {
    
                    $qty = $request->qty[$i];
                    $price = $request->price[$i];
    
                    if (($qty === null || $qty === '') && ($price === null || $price === '')) {
                        continue;
                    }
    
                    $dp->details()->create([
                        'nopol' => $request->nopol,
                        'length' => $length,
                        'qty' => $request->qty[$i],
                        'cubication' => (float)$request->cubication[$i],
                        'price' => $request->price[$i],
                        // kolom lainnya...
                    ]);
                }
            }
            DB::commit();

            return redirect()->route('down-payment.index', $type)->with('success', 'Data berhasil disimpan!');

        }catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal simpan lpb',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function edit($id, $type){
        $down_payment = Down_payment::with(['supplier', 'details'])->findOrFail($id);
        $suppliers = Supplier::all();
        $dp_types = [
            "DP",
            "Pelunasan"
        ];

        $down_payments = Down_payment::with(['supplier', "details"])
        ->where('dp_type', 'DP')
        ->where(function ($query) use ($down_payment) {
            $query->whereDoesntHave('children');
            
            // Jika sedang mengedit dan parent_id terisi, tampilkan juga DP itu
            if ($down_payment->parent_id) {
                $query->orWhere('id', $down_payment->parent_id);
            }
        })
        ->get();

        $availableLengths = [130, 260]; // panjang yang selalu ditampilkan

        $detailSource = $down_payment->dp_type === 'Pelunasan' && $down_payment->parent_id != null ? $down_payment->parent : $down_payment;   
        
        $detail_inputs = collect($availableLengths)->map(function($length) use ($detailSource) {
            $detail = $detailSource?->details->firstWhere('length', $length);
            return [
                'length' => $length,
                'qty' => $detail->qty ?? '',
                'cubication' => $detail->cubication ?? '',
                'price' => $detail->price ?? '',
            ];
        })->toArray();

        return view('pages.down-payments.edit', compact(['suppliers', 'down_payment', 'type', 'down_payments', 'detail_inputs', 'dp_types']));        
    }

    public function update(Request $request, $id, $type){
        DB::beginTransaction();

        try {
            $this->validate($request, [
                'date' => 'required|date',
                'nota_date' => 'required|date',
                'supplier' => 'required|exists:suppliers,id',
                'nominal' => 'required|numeric',
                'nopol' => 'required',
                'dp_type' => 'required|in:DP,Pelunasan',
            ]);

            // Temukan data existing
            $dp = Down_payment::with('details')->findOrFail($id);

            if($request->dp_type == "Pelunasan"){
                $this->validate($request, [
                'arrival_date' => 'required|date',
                ]);
                Down_payment::where("id", $request->dp_id)->update([
                    "arrival_date" => $request->arrival_date
                ]);
            }

            $dp->update([
                'date' => $request->date,
                'nota_date' => $request->nota_date,
                'arrival_date' => $request->arrival_date,
                'supplier_id' => $request->supplier,
                'nominal' => $request->nominal,
                'type' => $type,
                'dp_type' => $request->dp_type,
                'parent_id' => $request->dp_id,
            ]);

            // Hanya update detail jika bukan pelunasan
            if ($request->dp_type === 'DP' || $dp->parent_id == null) {
                // Hapus detail lama
                $dp->details()->delete();

                // Masukkan detail baru
                foreach ($request->length as $i => $length) {
                    $qty = $request->qty[$i];
                    $price = $request->price[$i];

                    // Jika kosong semua, skip
                    if (($qty === null || $qty === '') && ($price === null || $price === '')) {
                        continue;
                    }

                    $dp->details()->create([
                        'nopol' => $request->nopol,
                        'length' => $length,
                        'qty' => $qty,
                        'cubication' => (float) $request->cubication[$i],
                        'price' => $price,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('down-payment.index', $type)->with('success', 'Data berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal update data',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function destroy($id){
        $down_payment = Down_payment::with("children")->findOrFail($id);

        if(count($down_payment->children )> 0){
            foreach($down_payment->children as $children){
                $children->delete();
            }
        }

        $down_payment->delete();
        return redirect()->back()->with('status', 'deleted');
    }
}
