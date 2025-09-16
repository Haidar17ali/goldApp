<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Cutting;
use App\Models\CuttingDetail;
use Illuminate\Support\Facades\Validator;

class CuttingController extends Controller
{
    public function index(){
        return view("pages.cuttings.index");
    }

    public function create(){
        $products = Product::select(['id','name'])->get()->map(function($item){
            $item->name = strtoupper($item->name);
            return $item;
        });
        $colors = Color::select(['id','name'])->get()->map(function($item){
            $item->name = strtoupper($item->name);
            return $item;
        });
        $sizes = Size::select(['id','name'])->get()->map(function($item){
            $item->name = strtoupper($item->name);
            return $item;
        });

        return view('pages.cuttings.create', compact(['products', 'colors', 'sizes']));
    }

    public function store(Request $request)
    {
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
            if (empty($row['product']) || empty($row['color']) || empty($row['size']) || empty($row['qty'])) {
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

        $code = date("dmy", strtotime($request->date)) . substr($request->name, 0, 3);

        DB::beginTransaction();
        try {
            // 🔹 Simpan master cutting
            $cutting = Cutting::create([
                'code' => $code,
                'date' => $request->date,
                'tailor_name' => $request->name,
            ]);

            // 🔹 Simpan detail
            foreach ($details as $row) {
                CuttingDetail::create([
                    'cutting_id' => $cutting->id,
                    'product_id' => $row['product'],
                    'color_id'   => $row['color'],
                    'size_id'    => $row['size'],
                    'qty'        => $row['qty'],
                    'status'     => "Pending"
                ]);
            }

            DB::commit();

            return redirect()->route('cutting.index')
                ->with('success', 'Data cutting berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $th->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $cutting = Cutting::with('details')->findOrFail($id);

        $products = Product::select(['id', 'name'])->get();
        $colors = Color::select(['id', 'name'])->get();
        $sizes = Size::select(['id', 'name'])->get();

        // Konversi details untuk Handsontable
        $details = $cutting->details->map(function ($d) {
            return [
                "product" => $d->product_id,
                "color"   => $d->color_id,
                "size"    => $d->size_id,
                "qty"     => $d->qty,
            ];
        })->values();

        return view('pages.cuttings.edit', compact([
            'cutting',
            'products',
            'colors',
            'sizes',
            'details'
        ]));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'details' => 'required|json',
        ], [
            'date.required' => 'Tanggal wajib diisi.',
            'name.required' => 'Nama penjahit wajib diisi.',
            'details.required' => 'Detail produk wajib diisi.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput(); // << ini wajib supaya old() bisa jalan
        }
        
        DB::beginTransaction();
        try {
            $cutting = Cutting::findOrFail($id);
            $cutting->date = $request->date;
            $cutting->tailor_name = $request->name;
            $cutting->save();

            // Hapus detail lama
            CuttingDetail::where('cutting_id', $cutting->id)->delete();

            // Insert detail baru
            $details = json_decode($request->details, true);
            foreach ($details as $detail) {
                if (!empty($detail['product']) && !empty($detail['color']) && !empty($detail['size']) && !empty($detail['qty'])) {
                    CuttingDetail::create([
                        'cutting_id' => $cutting->id,
                        'product_id' => $detail['product'],
                        'color_id'   => $detail['color'],
                        'size_id'    => $detail['size'],
                        'qty'        => $detail['qty'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('cutting.index')->with('status', 'saved');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
