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
use Illuminate\Support\Facades\Log;

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

        $debts = Debt::with([
            "cuttingdetail.product:id,name",
            "cuttingdetail.color:id,name",
            "cuttingdetail.size:id,name",
            "cuttingdetail.cutting:id,date,tailor_name",
        ])
        ->where("status", "belum")
        ->whereDoesntHave("deliveryDetails")
        ->get()
        ->map(function($item){
            return[
                'id'           => $item->id,
                'product_name' => strtoupper($item->cuttingDetail->product->name ?? '-'),
                'color_name'   => strtoupper($item->cuttingDetail->color->name ?? '-'),
                'size_name'    => strtoupper($item->cuttingDetail->size->name ?? '-'),
                'cutting_date' => $item->cuttingDetail->cutting->date ?? null,
                'tailor_name'  => strtoupper($item->cuttingDetail->cutting->tailor_name ?? '-'),
                'source_type'  => 'debt',
                'qty'          => $item->qty,
            ];
        });

        // gabungkan jadi satu collection
        $cuttingDetails = $cuttingDetails->concat($debts)->values();

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
            $errorStatus = null; // 🔹 flag error

            // 🔹 Simpan master delivery
            $delivery = Delivery::create([
                'date' => $request->date,
                'sender' => $request->name,
                'create_by' => Auth::id(),
            ]);

            // 🔹 Simpan detail
            foreach ($details as $row) {
                if ($row["sourceType"] == "cutting") {
                    $cuttingDetail = CuttingDetail::with(["deliveryDetails", "cutting"])
                        ->findOrFail($row['cuttingDetail']);

                    $selisih = $cuttingDetail->qty - $row['qty'];

                    if ($selisih >= 0) {
                        Debt::create([
                            "tailor"            => $cuttingDetail->cutting->tailor_name ?? null, 
                            "cutting_detail_id" => $cuttingDetail->id,
                            "from"              => "cutting",
                            "qty"               => $selisih,
                            "status"            => "belum",
                        ]);
                    } else {
                        $errorStatus = "minus-qty";
                        break; // keluar loop tapi jangan return dulu
                    }
                } else {
                    $debt = Debt::with(["cuttingDetail"])->findOrFail($row["cuttingDetail"]);
                    $selisih = $debt->qty - $row['qty'];

                    if ($selisih >= 0) {
                        $debt->status = "sebagian";
                        $debt->save();
                    } elseif ($selisih == 0) {
                        $debt->status = "lunas";
                        $debt->save();
                    } else {
                        $errorStatus = "minus-qty";
                        break;
                    }
                }

                DeliveryDetail::create([
                    'delivery_id'  => $delivery->id,
                    'source_id'    => $row['cuttingDetail'],
                    'source_type'  => $row['sourceType'],
                    'qty'          => $row['qty'],
                ]);
            }

            if ($errorStatus) {
                DB::rollBack();
                return redirect()->route("pengiriman.buat")
                    ->with("status", $errorStatus);
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

    public function edit($id){
        $delivery = Delivery::with('details')->findOrFail($id);
        
        $cuttingDetails = CuttingDetail::with([
                'product:id,name',
                'color:id,name',
                'size:id,name',
                'cutting:id,date,tailor_name'
            ])
            ->where("status", "finish")
            ->where(function($q) use ($delivery) {
                $q->whereDoesntHave('deliveryDetails')
                ->orWhereHas('deliveryDetails', function($qq) use ($delivery) {
                    $qq->where('delivery_id', $delivery->id);
                });
            })
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

        $debts = Debt::with([
                "cuttingdetail.product:id,name",
                "cuttingdetail.color:id,name",
                "cuttingdetail.size:id,name",
                "cuttingdetail.cutting:id,date,tailor_name",
            ])
            ->where("status", "belum")
            ->where(function($q) use ($delivery) {
                $q->whereDoesntHave('deliveryDetails')
                ->orWhereHas('deliveryDetails', function($qq) use ($delivery) {
                    $qq->where('delivery_id', $delivery->id);
                });
            })
            ->get()
            ->map(function($item){
                return [
                    'id'           => $item->id,
                    'product_name' => strtoupper($item->cuttingDetail->product->name ?? '-'),
                    'color_name'   => strtoupper($item->cuttingDetail->color->name ?? '-'),
                    'size_name'    => strtoupper($item->cuttingDetail->size->name ?? '-'),
                    'cutting_date' => $item->cuttingDetail->cutting->date ?? null,
                    'tailor_name'  => strtoupper($item->cuttingDetail->cutting->tailor_name ?? '-'),
                    'source_type'  => 'debt',
                    'qty'          => $item->qty,
                ];
            });


        // gabungkan jadi satu collection
        $cuttingDetails = $cuttingDetails->concat($debts)->values();

        // Konversi details untuk Handsontable
        $details = $delivery->details->map(function ($d) {
            return [
                "cuttingDetail" => $d->source_id,
                "qty"     => $d->qty,
            ];
        })->values();

        return view('pages.deliveries.edit', compact([
            'delivery',
            'cuttingDetails',
            'details'
        ]));
    }




   public function update(Request $request, $id){
        $request->validate([
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'details' => ['required', 'json'],
        ]);

        $details = json_decode($request->details, true);
        if (!$details || !is_array($details)) {
            throw ValidationException::withMessages([
                'details' => 'Detail produk tidak valid.',
            ]);
        }

        DB::beginTransaction();
        try {
            $errorStatus = null;

            $delivery = Delivery::with("details")->findOrFail($id);

            // 🔹 Rollback efek lama
            foreach ($delivery->details as $oldDetail) {
                if ($oldDetail->source_type == "cutting") {
                    // hapus debt lama terkait
                    Debt::where("cutting_detail_id", $oldDetail->source_id)
                        ->where("from", "cutting")
                        ->delete();
                } else {
                    // hapus debt lama terkait dari hutang
                    Debt::where("id", $oldDetail->source_id)->delete();
                }
            }

            // 🔹 Hapus detail lama
            $delivery->details()->delete();

            // 🔹 Update master delivery
            $delivery->update([
                'date' => $request->date,
                'sender' => $request->name,
            ]);

            // 🔹 Proses detail baru
            foreach ($details as $row) {
                if ($row["sourceType"] == "cutting") {
                    $cuttingDetail = CuttingDetail::with("cutting")->findOrFail($row['cuttingDetail']);
                    $selisih = $cuttingDetail->qty - $row['qty'];

                    if ($selisih >= 0) {
                        Debt::create([
                            "tailor"            => $cuttingDetail->cutting->tailor_name ?? null,
                            "cutting_detail_id" => $cuttingDetail->id,
                            "from"              => "cutting",
                            "qty"               => $selisih,
                            "status"            => "belum",
                        ]);
                    } else {
                        $errorStatus = "minus-qty";
                        break;
                    }
                } else {
                    $debt = Debt::with("cuttingDetail")->findOrFail($row["cuttingDetail"]);
                    $selisih = $debt->qty - $row['qty'];

                    if ($selisih >= 0) {
                        $debt->status = "sebagian";
                    } elseif ($selisih == 0) {
                        $debt->status = "lunas";
                    } else {
                        $errorStatus = "minus-qty";
                        break;
                    }
                    $debt->save();
                }

                DeliveryDetail::create([
                    'delivery_id'  => $delivery->id,
                    'source_id'    => $row['cuttingDetail'],
                    'source_type'  => $row['sourceType'],
                    'qty'          => $row['qty'],
                ]);
            }

            if ($errorStatus) {
                DB::rollBack();
                return redirect()->route("pengiriman.ubah", $id)
                    ->with("status", $errorStatus);
            }

            DB::commit();
            return redirect()->route('pengiriman.index')
                ->with('status', 'updated');

        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $th->getMessage()])
                ->withInput();
        }
    }




    public function destroy($id){
        DB::transaction(function () use ($id) {
            $delivery = Delivery::with(["details.source.debt"])->findOrFail($id);

            if ($delivery->details->count()) {
                foreach ($delivery->details as $detail) {
                    // hapus relasi morphMany / hasMany
                    if($detail->source != null){
                        if(count($detail->source->debt)){
                            $detail->source->debt()->delete();
                        }
                    }
                    $detail->delete();
                }
            }
            $delivery->delete();
        });
        return redirect()->back()->with("status", "deleted");
    }

}
