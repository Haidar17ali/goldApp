<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\CuttingDetail;
use App\Models\Debt;
use App\Models\Delivery;
use App\Models\DeliveryDetail;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index(){
        return view("pages.deliveries.index");
    }
    public function create(){
        $cuttingDetails = CuttingDetail::with([
            'product:id,name',
            'color:id,name',
            'size:id,name',
            'cutting:id,date,tailor_name'
        ])
        ->where("status", "finish")
        ->whereDoesntHave('deliveryDetails') // morphMany tetap bisa difilter begini
        ->get()
        ->map(function($item) {
            return [
                'id'           => $item->id,
                'product_name' => strtoupper($item->product->name ?? '-'),
                'color_name'   => strtoupper($item->color->name ?? '-'),
                'size_name'    => strtoupper($item->size->name ?? '-'),
                'cutting_date' => $item->cutting->date ?? null,
                'tailor_name'  => strtoupper($item->cutting->tailor_name ?? '-'),
                'source_type'  => 'cutting',
                'qty'          => $item->qty,
            ];
        });

        return view("pages.deliveries.create", compact(["cuttingDetails"]));
    }

    public function store(Request $request){
        // 🔹 Validasi request utama
        $request->validate([
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'details' => ['required', 'json'],
        ]);

        // Decode details
        $details = json_decode($request->details, true);

        if (!$details || !is_array($details)) {
            throw ValidationException::withMessages([
                'details' => 'Detail produk tidak valid.',
            ]);
        }

        // 🔹 Validasi tiap baris details
        foreach ($details as $i => $row) {
            if (empty($row['cuttingDetail'])) {
                throw ValidationException::withMessages([
                    'details' => "Detail baris ke-" . ($i + 1) . " belum lengkap.",
                ]);
            }

            if (!is_numeric($row['qty']) || $row['qty'] <= 0) {
                throw ValidationException::withMessages([
                    'details' => "Jumlah pada baris ke-" . ($i + 1) . " harus lebih dari 0.",
                ]);
            }
        }


        DB::beginTransaction();
        try {
            // 🔹 Simpan master cutting
            $delivery = Delivery::create([
                'date' => $request->date,
                'sender' => $request->name,
                'create_by' => Auth::id(),
            ]);

            // 🔹 Simpan detail
            foreach ($details as $row) {
                // check selisih qty
                if($row["sourceType"] == "cutting"){
                    $cuttingDetail = CuttingDetail::with(["deliveryDetails", "cutting"])->findOrFail($row['cuttingDetail']);
                    
                     // Hitung selisih
                    $selisih = $cuttingDetail->qty - $row['qty'];

                    if ($selisih > 0) {
                        // Simpan ke Debt
                        Debt::create([
                            "tailor"            => $cuttingDetail->cutting->tailor_name ?? null, 
                            "cutting_detail_id" => $cuttingDetail->id,
                            "from"              => "cutting", // misalnya asal hutang ini dari proses delivery
                            "qty"               => $selisih,
                            "status"            => "belum",  // atau default status sesuai kebutuhan
                        ]);
                    }
                }

                DeliveryDetail::create([
                    'delivery_id' => $delivery->id,
                    'source_id'   => $row['cuttingDetail'],
                    'source_type'    => $row['sourceType'],
                    'qty'        => $row['qty'],
                ]);
            }

            DB::commit();

            return redirect()->route('pengiriman.index')
                ->with('status', 'saved');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $th->getMessage()])
                ->withInput();
        }
    }
}
