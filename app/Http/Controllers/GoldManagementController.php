<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\GoldManagement;
use App\Models\GoldManagementDetail;
use App\Helpers\StockHelper;
use App\Models\Karat;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoldManagementController extends BaseController
{
    public function index()
    {
        $managements = GoldManagement::with('creator')->latest()->paginate(20);
        return view('pages.gold-management.index', compact('managements'));
    }

    public function create()
    {
        // Ambil semua karat yang sedang punya stok customer
        $karats = \App\Models\Karat::whereHas('stocks', function ($q) {
            $q->where('type', 'customer')
                ->where('weight', '>', 0);
        })->get();

        // Jenis pengelolaan tetap manual
        $types = [
            'sepuh' => 'Sepuh',
            'patri' => 'Patri',
            'rosok' => 'Rosok',
        ];

        return view('pages.gold-management.create', compact('karats', 'types'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:sepuh,patri,rosok',
            'karat_id' => 'required|exists:karats,id',
            'gram_out' => 'required|numeric|min:0.01',
            'gram_in' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        // PRODUCT EMAS
        $product = Product::firstOrCreate(['code' => "ems"], ['name' => 'emas']);

        // ✅ VALIDASI STOK DILUAR TRANSACTION
        $customerStock = Stock::where('product_id', $product->id)
            ->where('karat_id', $request->karat_id)
            ->where('type', 'customer')
            ->first();

        if (!$customerStock) {
            return back()
                ->withErrors(['karat_id' => 'Tidak ditemukan stok customer untuk karat ini.'])
                ->withInput();
        }

        if ($customerStock->weight < $request->gram_out) {
            return back()
                ->withErrors(['gram_out' => 'Stok customer tidak mencukupi untuk karat ini.'])
                ->withInput();
        }


        // ✅ SEMUA PROSES 100% HARUS BERHASIL → PAKAI TRANSAKSI
        DB::transaction(function () use ($request, $product, $customerStock) {

            $userId = auth()->id();
            $branchId = 2;
            $storageId = 1;

            // ✅ Simpan record GoldManagement
            $management = GoldManagement::create([
                'date' => $request->date,
                'type' => $request->type,
                'product_id' => $product->id,
                'karat_id' => $request->karat_id,
                'gram_out' => $request->gram_out,
                'gram_in' => $request->gram_in,
                'note' => $request->note,
                'created_by' => $userId,
            ]);

            // ✅ Mutasi stok keluar
            StockHelper::moveStock(
                $product->id,
                $request->karat_id,
                $branchId,
                $storageId,
                'out',
                1,
                $request->gram_out,
                'GoldManagement',
                $management->id,
                'Pengelolaan emas keluar (' . $request->type . ')',
                $userId,
                'customer'
            );

            // ✅ Tentukan hasil
            if ($request->type === 'rosok') {
                $goldType = 'batangan';

                $karat24 = Karat::firstOrCreate(['name' => '24K']);
                $karatIdIn = $karat24->id;
            } else {
                $goldType = 'second';
                $karatIdIn = $request->karat_id;
            }

            // ✅ Mutasi stok masuk
            StockHelper::moveStock(
                $product->id,
                $karatIdIn,
                $branchId,
                $storageId,
                'in',
                1,
                $request->gram_in,
                'GoldManagement',
                $management->id,
                'Hasil pengelolaan emas (' . ucfirst($request->type) . ')',
                $userId,
                $goldType
            );
        });

        return redirect()
            ->route('pengelolaan-emas.index')
            ->with('status', 'saved');
    }

    public function edit($id)
    {
        $management = GoldManagement::findOrFail($id);

        $karats = Karat::where(function ($query) use ($management) {
            $query->whereHas('stocks', function ($q) {
                $q->where('type', 'customer')->where('weight', '>', 0);
            })
                ->orWhere('id', $management->karat_id); // pastikan karat yg digunakan tetap tampil
        })->get();

        $types = [
            'sepuh' => 'Sepuh',
            'patri' => 'Patri',
            'rosok' => 'Rosok',
        ];

        return view('pages.gold-management.edit', compact('management', 'karats', 'types'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:sepuh,patri,rosok',
            'karat_id' => 'required|exists:karats,id',
            'gram_out' => 'required|numeric|min:0.01',
            'gram_in' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $id) {

            $management = GoldManagement::findOrFail($id);

            $product = Product::firstOrCreate(['code' => 'ems', 'name' => 'emas']);
            $branchId = 2;
            $storageId = 1;
            $userId = auth()->id();

            // 1️⃣ Rollback stok lama
            StockHelper::moveStock(
                $product->id,
                $management->karat_id,
                $branchId,
                $storageId,
                'in', // karena sebelumnya 'out'
                1,
                $management->gram_out,
                'GoldManagement',
                $management->id,
                'Rollback pengelolaan emas keluar (edit)',
                $userId,
                'customer'
            );

            // Tentukan karat hasil lama (untuk rollback 'in')
            $karatIdOldIn = $management->type === 'rosok'
                ? Karat::firstWhere('name', '24K')->id
                : $management->karat_id;

            StockHelper::moveStock(
                $product->id,
                $karatIdOldIn,
                $branchId,
                $storageId,
                'out', // karena sebelumnya 'in'
                1,
                $management->gram_in,
                'GoldManagement',
                $management->id,
                'Rollback hasil pengelolaan emas (edit)',
                $userId,
                $management->type === 'rosok' ? 'batangan' : 'second'
            );

            // 2️⃣ Validasi stok baru
            $customerStock = Stock::where('product_id', $product->id)
                ->where('karat_id', $request->karat_id)
                ->where('type', 'customer')
                ->first();

            if (!$customerStock) {
                throw ValidationException::withMessages([
                    'karat_id' => 'Tidak ditemukan stok customer untuk karat ini.'
                ]);
            }

            if ($customerStock->weight < $request->gram_out) {
                throw ValidationException::withMessages([
                    'gram_out' => 'Stok customer tidak mencukupi untuk karat ini.'
                ]);
            }

            // 3️⃣ Update data utama
            $management->update([
                'date' => $request->date,
                'type' => $request->type,
                'karat_id' => $request->karat_id,
                'gram_out' => $request->gram_out,
                'gram_in' => $request->gram_in,
                'note' => $request->note,
            ]);

            // 4️⃣ Mutasi baru keluar dari customer
            StockHelper::moveStock(
                $product->id,
                $request->karat_id,
                $branchId,
                $storageId,
                'out',
                1,
                $request->gram_out,
                'GoldManagement',
                $management->id,
                'Edit pengelolaan emas keluar (' . $request->type . ')',
                $userId,
                'customer'
            );

            // 5️⃣ Mutasi baru masuk ke hasil
            if ($request->type === 'rosok') {
                $goldType = 'batangan';
                $karat24 = Karat::firstOrCreate(['name' => '24K']);
                $karatIdIn = $karat24->id;
            } else {
                $goldType = 'second';
                $karatIdIn = $request->karat_id;
            }
            StockHelper::moveStock(
                $product->id,
                $karatIdIn,
                $branchId,
                $storageId,
                'in',
                1,
                $request->gram_in,
                'GoldManagement',
                $management->id,
                'Edit hasil pengelolaan emas (' . ucfirst($request->type) . ')',
                $userId,
                $goldType
            );
        });

        return redirect()
            ->route('pengelolaan-emas.index')
            ->with('success', 'Data pengelolaan emas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $management = GoldManagement::findOrFail($id);

        // Gunakan transaksi agar data konsisten
        \DB::transaction(function () use ($management) {

            // Kembalikan stok customer
            $customerStock = \App\Models\Stock::where('karat_id', $management->karat_id)
                ->where('type', 'customer')
                ->first();

            if ($customerStock) {
                $customerStock->weight += $management->gram_out;
                $customerStock->save();
            }

            // Kurangi stok second
            $secondStock = \App\Models\Stock::where('karat_id', $management->karat_id)
                ->where('type', 'second')
                ->first();

            if ($secondStock) {
                $secondStock->weight -= $management->gram_in;
                if ($secondStock->weight < 0) {
                    $secondStock->weight = 0; // jaga-jaga biar ga minus
                }
                $secondStock->save();
            }

            // Hapus data pengelolaan emas
            $management->delete();
        });

        return redirect()
            ->route('pengelolaan-emas.index')
            ->with('success', 'Data pengelolaan emas berhasil dihapus dan stok dikembalikan.');
    }
}
