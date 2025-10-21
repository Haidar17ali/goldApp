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

    public function store($type, $purchaseType, Request $request)
{
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
        'details.*.variant_label'   => 'required|string|max:255',
        'details.*.qty'             => 'required|numeric|min:0.001',
        'details.*.price_per_gram'  => 'required|numeric|min:0',
        'details.*.note'            => 'nullable|string|max:255',
    ], [
        'details.required' => 'Minimal harus ada satu item transaksi.',
        'details.*.variant_label.required' => 'Varian tidak boleh kosong.',
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
                // Parse variant_label, format: "Produk - Karat - Gram"
                $variantLabel = $detail['variant_label'];
                $parts = preg_split('/\s*-\s*/', $variantLabel);

                $productName = $parts[0] ?? null;
                $karatName   = $parts[1] ?? null;
                $gram        = isset($parts[2]) ? (float) str_replace(['g','G'], '', $parts[2]) : 0;

                if (!$productName || !$karatName) {
                    throw new \Exception("Format varian tidak valid untuk '{$variantLabel}'");
                }

                // firstOrCreate product & karat
                $product = \App\Models\Product::firstOrCreate(
                    ['name' => trim($productName)],
                    ['code' => \Str::slug(trim($productName))] // optional tambahan field
                );

                $karat = \App\Models\Karat::firstOrCreate(
                    ['name' => trim($karatName)]
                );

                // firstOrCreate variant
                $variant = \App\Models\ProductVariant::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'karat_id' => $karat->id,
                        'gram' => $gram,
                    ],
                    [
                        'sku' => strtoupper(($product->name ?? 'PROD') . '-' . ($karat->name ?? 'KRT') . '-' . ($gram ?: 'GEN')),
                        'barcode' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12)),
                        'default_price' => 0,
                    ]
                );

                // Hitung subtotal & simpan detail
                $qty = (float) $detail['qty'];
                $price = (float) $detail['price_per_gram'];
                $subtotal = $gram * $price *$qty;
                $total += $subtotal;

                $transactionDetail = \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $variant->id,
                    'qty' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                    'note' => $detail['note'] ?? null,
                ]);

                // Update stok: gunakan StockHelper (pastikan function menerima nullable branch/storage)
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
                    null // userId optional
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


}
