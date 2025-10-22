<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Helpers\StockHelper;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index($type, $purchaseType)
    {
        $transactions = Transaction::latest()->paginate(20);
        return view('pages.transactions.index', compact('transactions', "type", "purchaseType"));
    }

    public function create($type, $purchaseType)
    {
        $variants = \App\Models\ProductVariant::with(['product','karat'])
        ->orderBy('id', 'desc')
        ->get()
        ->map(function($v){
            return [
                'id' => $v->id,
                'label' => ($v->product->name ?? 'Produk') 
                          . ' - ' . ($v->karat->name ?? '-') 
                          . ' - ' . ($v->gram ?? '-') . 'g',
                'product_name' => $v->product->name ?? null,
                'karat_name'   => $v->karat->name ?? null,
                'gram'         => $v->gram,
            ];
        });
        return view('pages.transactions.create', compact(["type","purchaseType", "variants"]));
    }

    public function store($type, $purchaseType, Request $request){
        // Ambil semua input (agar bisa modifikasi details sebelum validasi)
        $data = $request->all();

        // Jika details dikirim sebagai JSON string (dari Handsontable), decode dulu
        if (isset($data['details']) && is_string($data['details'])) {
            $decoded = json_decode($data['details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['details' => 'Format details tidak valid (JSON error).']);
            }
            $data['details'] = $decoded;
        }

        // Sekarang lakukan validasi pada $data (bukan $request langsung)
        $validator = \Validator::make($data, [
                'invoice_number' => 'nullable|string|max:255',
                'customer_name'  => 'nullable|string|max:255',
                'supplier_name'  => 'nullable|string|max:255',
                'note'           => 'nullable|string|max:1000',
                'details'        => 'required|array|min:1',
                // Accept either variant_label OR manual fields
                'details.*.variant_label'   => 'nullable|string|max:255',
                'details.*.product_name'    => 'required_without:details.*.variant_label|string|max:255',
                'details.*.karat_name'      => 'required_without:details.*.variant_label|string|max:100',
                'details.*.gram'            => 'required_without:details.*.variant_label|numeric|min:0.001',

                // qty & price rules
                'details.*.qty'             => 'required|numeric|min:0.001',
                'details.*.price_per_gram'  => 'required|numeric|min:0',
                'details.*.note'            => 'nullable|string|max:255',
            ], [
                'details.required' => 'Minimal harus ada satu item transaksi.',
                'details.*.product_name.required_without' => 'Nama produk/varian harus diisi jika varian tidak dipilih.',
                'details.*.karat_name.required_without'   => 'Karat harus diisi jika varian tidak dipilih.',
                'details.*.gram.required_without'         => 'Gram harus diisi jika varian tidak dipilih.',
                'details.*.qty.min' => 'Qty minimal 0.001.',
            ]);

        if ($validator->fails()) {
            // encode details agar bisa dikembalikan ke view
            $oldDetails = json_encode($data['details'] ?? []);
            return back()
                ->withInput($request->except('details') + ['details' => $oldDetails])
                ->withErrors($validator);
        }

        // Gunakan $data yang sudah tervalidasi untuk proses selanjutnya
        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($validated, $type, $purchaseType, &$transaction) {

            

                // Buat transaksi header
                $transaction = \App\Models\Transaction::create([
                    'type' => $type,
                    'branch_id' => 2,
                    'storage_location_id' => 1,
                    'purchase_type' => $purchaseType,
                    'transaction_date' => now(),
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'created_by' => auth()->id(),
                    'total' => 0,
                ]);

                $total = 0;

                foreach ($validated['details'] as $detail) {
                    // Jika variant_label ada dan tidak kosong → parsing dari sana
                    if (!empty($detail['variant_label'])) {
                        $variantLabel = $detail['variant_label'];
                        $parts = preg_split('/\s*-\s*/', $variantLabel);

                        $productName = $parts[0] ?? null;
                        $karatName   = $parts[1] ?? null;
                        $gram        = isset($parts[2]) ? (float) str_replace(['g', 'G'], '', $parts[2]) : 0;
                    } else {
                        // Jika variant_label kosong → ambil dari kolom manual
                        $productName = trim($detail['product_name'] ?? '');
                        $karatName   = trim($detail['karat_name'] ?? '');
                        $gram        = (float) ($detail['gram'] ?? 0);
                    }

                    // Validasi minimal agar tidak ada field kosong total
                    if (!$productName || !$karatName || $gram <= 0) {
                        throw new \Exception("Data varian tidak lengkap pada salah satu baris (produk, karat, atau gram kosong).");
                    }

                    // firstOrCreate product & karat
                    $product = \App\Models\Product::firstOrCreate(
                        ['name' => $productName],
                        ['code' => \Str::slug($productName)]
                    );

                    $karat = \App\Models\Karat::firstOrCreate(
                        ['name' => $karatName]
                    );

                    // firstOrCreate variant (pakai kombinasi product + karat + gram)
                    $variant = \App\Models\ProductVariant::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'karat_id' => $karat->id,
                            'gram' => $gram,
                        ],
                        [
                            'sku' => strtoupper(($productName ?: 'PROD') . '-' . ($karatName ?: 'KRT') . '-' . ($gram ?: 'GEN')),
                            'barcode' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12)),
                            'default_price' => 0,
                        ]
                    );

                    // Hitung subtotal
                    $qty = (float) $detail['qty'];
                    $price = (float) $detail['price_per_gram'];
                    $subtotal = $gram * $price * $qty;
                    $total += $subtotal;

                    // Simpan detail transaksi
                    $transactionDetail = \App\Models\TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_variant_id' => $variant->id,
                        'qty' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal,
                        'note' => $detail['note'] ?? null,
                    ]);

                    // Update stok
                    $movementType = $transaction->type === 'purchase' ? 'in' : 'out';
                    \App\Helpers\StockHelper::moveStock(
                        $variant->id,
                        $transaction->branch_id ?? null,
                        $transaction->storage_location_id ?? null,
                        $movementType,
                        $qty,
                        $variant->gram,
                        'Transaction',
                        $transaction->id,
                        null
                    );
                }


                // update total
                $transaction->update(['total' => $total]);
            });

            return redirect()->route('transaksi.index', ["type"=>$type,"purchaseType" =>$purchaseType])->with('status', 'saved');

        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['msg' => 'Transaksi gagal: ' . $e->getMessage()]);
        }
    }

    public function edit($type, $purchaseType,Transaction $transaction){
        $transaction->load('details.productVariant.product', 'details.productVariant.karat');

        $variants = \App\Models\ProductVariant::with('product', 'karat')->get()->map(function ($v) {
            return [
                'id' => $v->id,
                'label' => "{$v->product->name} - {$v->karat->name} - {$v->gram}g",
                'product_name' => $v->product->name,
                'karat_name' => $v->karat->name,
                'gram' => $v->gram,
            ];
        });

        $details = $transaction->details->map(function ($d) {
            return [
                'variant_label' => $d->productVariant ? "{$d->productVariant->product->name} - {$d->productVariant->karat->name} - {$d->productVariant->gram}g" : '',
                'product_name' => $d->productVariant?->product->name ?? '',
                'karat_name' => $d->productVariant?->karat->name ?? '',
                'gram' => $d->productVariant?->gram ?? 0,
                'qty' => $d->qty,
                'price_per_gram' => $d->unit_price,
                'subtotal' => $d->subtotal,
                'note' => $d->note,
            ];
        });

        return view('pages.transactions.edit', [
            'transaction' => $transaction,
            'details' => $details,
            'variants' => $variants,
            'type' => $transaction->type,
            'purchaseType' => $transaction->purchase_type,
        ]);
    }

    public function update(Request $request,$type, $purchaseType, Transaction $transaction){
        // decode details jika string dari Handsontable
        $data = $request->all();
        if (isset($data['details']) && is_string($data['details'])) {
            $data['details'] = json_decode($data['details'], true);
        }

        // validasi sama seperti store (variant_label OR manual fields)
        $validator = \Validator::make($data, [
            'invoice_number' => 'nullable|string|max:255',
            'customer_name'  => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:1000',
            'details'        => 'required|array|min:1',
            'details.*.variant_label'   => 'nullable|string|max:255',
            'details.*.product_name'    => 'required_without:details.*.variant_label|string|max:255',
            'details.*.karat_name'      => 'required_without:details.*.variant_label|string|max:100',
            'details.*.gram'            => 'required_without:details.*.variant_label|numeric|min:0.001',
            'details.*.qty'             => 'required|numeric|min:0.001',
            'details.*.price_per_gram'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $oldDetails = json_encode($data['details'] ?? []);
            return back()->withInput($request->except('details') + ['details' => $oldDetails])->withErrors($validator);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($transaction, $validated) {
                // rollback stok lama
                foreach ($transaction->details as $detail) {
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_variant_id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        'out', // balik stok
                        $detail->qty,
                        $detail->variant?->gram ?? null,
                        'Transaction',
                        $transaction->id,
                        "old-edited-data",
                    );
                }

                // hapus detail lama
                $transaction->details()->delete();

                $total = 0;

                // simpan detail baru
                foreach ($validated['details'] as $d) {
                    // parsing variant sama seperti store
                    $variantLabel = $d['variant_label'];
                    $parts = preg_split('/\s*-\s*/', $variantLabel);
                    $productName = $parts[0] ?? $d['product_name'];
                    $karatName = $parts[1] ?? $d['karat_name'];
                    $gram = isset($parts[2]) ? (float) str_replace(['g','G'], '', $parts[2]) : (float)($d['gram'] ?? 0);

                    $product = \App\Models\Product::firstOrCreate(['name' => trim($productName)]);
                    $karat = \App\Models\Karat::firstOrCreate(['name' => trim($karatName)]);
                    $variant = \App\Models\ProductVariant::firstOrCreate(
                        ['product_id' => $product->id, 'karat_id' => $karat->id, 'gram' => $gram],
                        ['sku' => strtoupper($productName . '-' . $karatName . '-' . $gram)]
                    );

                    $qty = (float)$d['qty'];
                    $price = (float)$d['price_per_gram'];
                    $subtotal = $qty * $price * $gram;
                    $total += $subtotal;

                    $td = \App\Models\TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_variant_id' => $variant->id,
                        'qty' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal,
                        'note' => $d['note'] ?? null,
                    ]);

                    // update stok baru
                    \App\Helpers\StockHelper::moveStock(
                        $variant->id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        'in',
                        $qty,
                        $gram,
                        'Transaction',
                        $transaction->id
                    );
                }

                $transaction->update([
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'customer_name'  => $validated['customer_name'] ?? null,
                    'note'           => $validated['note'] ?? null,
                    'total'          => $total,
                ]);
            });

            return redirect()->route('transaksi.index',['type' => $type, 'purchaseType' => $purchaseType])->with('status', 'edited');

        } catch (\Throwable $e) {
            \Log::error('Gagal update transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->withErrors(['msg' => 'Update gagal: ' . $e->getMessage()]);
        }
    }

    public function destroy($type, $purchaseType,Transaction $transaction){
        try {
            DB::transaction(function () use ($transaction) {
                // rollback semua stok
                foreach ($transaction->details as $detail) {
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_variant_id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        'out',
                        $detail->qty,
                        $detail->variant?->gram ?? null,
                        'Transaction',
                        $transaction->id,
                        "deleted"
                    );
                }

                // hapus detail
                $transaction->details()->delete();

                // hapus header
                $transaction->delete();
            });

            return redirect()->route('transaksi.index', ['type' => $type, 'purchaseType' => $purchaseType])->with('status', 'deleted');
        } catch (\Throwable $e) {
            \Log::error('Gagal hapus transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['msg' => 'Hapus gagal: ' . $e->getMessage()]);
        }
    }




}
