<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Helpers\StockHelper;
use App\Models\BankAccount;
use App\Models\Karat;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index($type, $purchaseType)
    {
        $transactions = Transaction::latest()->paginate(20);
        return view('pages.transactions.index', compact('transactions', "type", "purchaseType"));
    }

    protected function generateUniqueInvoiceNumber()
    {
        do {
            $invoice = 'INV-' . strtoupper(Str::random(6));
        } while (Transaction::where('invoice_number', $invoice)->exists());

        return $invoice;
    }

    public function create($type, $purchaseType)
    {
        $bankAccounts = BankAccount::orderBy("id", "desc")->get();
        $invoiceNumber = $this->generateUniqueInvoiceNumber();

        // ambil daftar products & karats untuk dropdown (kirim sebagai array nama)
        $products = \App\Models\Product::orderBy('name')->get()->map(function ($p) {
            return $p->name;
        })->values()->toArray();

        $karats = \App\Models\Karat::orderBy('name')->get()->map(function ($k) {
            return $k->name;
        })->values()->toArray();
        return view('pages.transactions.create', compact(["type", "purchaseType", "invoiceNumber", "products", "karats", "bankAccounts"]));
    }

    public function store($type, $purchaseType, Request $request)
    {
        $data = $request->all();

        // Decode JSON details jika dikirim sebagai string
        if (isset($data['details']) && is_string($data['details'])) {
            $decoded = json_decode($data['details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['details' => 'Format detail tidak valid.']);
            }
            $data['details'] = $decoded;
        }

        // ✅ Validasi dasar
        $validator = \Validator::make($data, [
            'invoice_number'  => 'nullable|string|max:255',
            'customer_name'   => 'nullable|string|max:255',
            'note'            => 'nullable|string|max:1000',
            'payment_method'  => 'required|string|in:cash,transfer,cash_transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'transfer_amount' => 'nullable|numeric|min:0',
            'cash_amount'     => 'nullable|numeric|min:0',
            'reference_no'    => 'nullable|string|max:255',
            'details'         => 'required|array|min:1',
            'details.*.product_name' => 'required|string|max:255',
            'details.*.karat_name'   => 'required|string|max:100',
            'details.*.gram'         => 'required|numeric|min:0.001',
            'details.*.harga_beli'   => 'nullable|numeric|min:0',
            'details.*.harga_jual'   => 'nullable|numeric|min:0',
        ], [
            'details.required' => 'Minimal satu barang harus diisi.',
        ]);

        // Tambah validasi foto jika penjualan
        if ($type === 'penjualan') {
            $validator->addRules([
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        // 🔹 Upload foto (hanya untuk penjualan)
        if ($type === 'penjualan' && $request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/transaction_photos');
            $validated['photo'] = str_replace('public/', 'storage/', $path);
        }

        // 🔸 Validasi metode pembayaran
        if ($validated['payment_method'] === 'transfer') {
            if (empty($validated['bank_account_id'])) {
                return back()->withInput()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk transfer.']);
            }
            $validated['cash_amount'] = 0;
        }

        if ($validated['payment_method'] === 'cash') {
            $validated['transfer_amount'] = 0;
            $validated['bank_account_id'] = null;
        }

        if ($validated['payment_method'] === 'cash_transfer') {
            if (
                (empty($validated['cash_amount']) && empty($validated['transfer_amount'])) ||
                (($validated['cash_amount'] ?? 0) + ($validated['transfer_amount'] ?? 0)) <= 0
            ) {
                return back()->withInput()->withErrors(['cash_amount' => 'Nominal tunai & transfer wajib diisi.']);
            }

            if (empty($validated['bank_account_id'])) {
                return back()->withInput()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk kombinasi.']);
            }
        }

        try {
            DB::transaction(function () use ($validated, $type, $purchaseType) {

                // 🧾 Buat header transaksi
                $transaction = \App\Models\Transaction::create([
                    'type'                => $type,
                    'purchase_type'       => $purchaseType,
                    'branch_id'           => 2,
                    'storage_location_id' => 1,
                    'transaction_date'    => now(),
                    'invoice_number'      => $validated['invoice_number'] ?? null,
                    'customer_name'       => $validated['customer_name'] ?? null,
                    'note'                => $validated['note'] ?? null,
                    'created_by'          => auth()->id(),
                    'total'               => 0,
                    'photo'               => $validated['photo'] ?? null, // ✅ Simpan foto jika penjualan
                    'payment_method'      => $validated['payment_method'],
                    'bank_account_id'     => $validated['bank_account_id'] ?? null,
                    'transfer_amount'     => $validated['transfer_amount'] ?? 0,
                    'cash_amount'         => $validated['cash_amount'] ?? 0,
                    'reference_no'        => $validated['reference_no'] ?? null,
                ]);

                $total = 0;

                foreach ($validated['details'] as $detail) {
                    $productName = trim($detail['product_name']);
                    $karatName   = trim($detail['karat_name']);
                    $gram        = (float) $detail['gram'];

                    // Tentukan harga berdasarkan type transaksi
                    $price = $type === 'penjualan'
                        ? (float) ($detail['harga_jual'] ?? 0)
                        : (float) ($detail['harga_beli'] ?? 0);

                    $qty = 1; // karena tidak ada kolom qty di form baru
                    $subtotal = $price;
                    $total += $subtotal;

                    // Buat product dan karat jika belum ada
                    $product = \App\Models\Product::firstOrCreate(
                        ['name' => $productName],
                        ['code' => \Str::slug($productName)]
                    );

                    $karat = \App\Models\Karat::firstOrCreate(['name' => $karatName]);

                    // Tentukan jenis emas (rosok, sepuh, new)
                    // $goldType = $purchaseType === 'sepuh'
                    //     ? 'sepuh'
                    //     : ($purchaseType === 'pabrik' ? 'new' : 'rosok');

                    // Simpan detail transaksi
                    \App\Models\TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $product->id,
                        'karat_id'       => $karat->id,
                        'gram'           => $gram,
                        'unit_price'     => $price,
                        'type'           => $purchaseType,
                        'note'           => $detail['note'] ?? null,
                    ]);

                    // Update stok (in/out)
                    $movementType = $transaction->type === 'purchase' ? 'in' : 'out';
                    if($purchaseType == "customer"){
                        $product = Product::where("name", "emas")->first();
                    }

                    \App\Helpers\StockHelper::moveStock(
                        $product->id,
                        $karat->id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        $movementType,
                        $qty, // selalu 1
                        $gram,
                        'Transaction',
                        $transaction->id,
                        'create-transaction',
                        auth()->id(),
                        $purchaseType
                    );
                }

                $transaction->update(['total' => $total]);
            });

            return redirect()
                ->route('transaksi.index', ['type' => $type, 'purchaseType' => $purchaseType])
                ->with('status', 'saved');
        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['msg' => 'Transaksi gagal: ' . $e->getMessage()]);
        }
    }


    public function edit($type, $purchaseType, $id)
    {
        $transaction = Transaction::with(['details.product', "details.karat"])->findOrFail($id);

        $products = Product::select("id", 'name')->get();
        $karats = Karat::select("id", 'name')->get();
        $bankAccounts = BankAccount::all();

        // Format data details agar sesuai untuk JS
        $details = $transaction->details->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name ?? '',
                'karat_id' => $item->karat_id,
                'karat_name' => $item->karat->name ?? '',
                'gram' => $item->gram,
                'harga_beli' => $item->unit_price,
            ];
        });

        return view('pages.transactions.edit', compact(
            'transaction',
            'type',
            'purchaseType',
            'bankAccounts',
            'products',
            'karats',
            'details'
        ));
    }


    // 🟢 UPDATE
    public function update($type, $purchaseType, $id, Request $request)
    {
        $data = $request->all();

        // Decode JSON details jika dikirim sebagai string
        if (isset($data['details']) && is_string($data['details'])) {
            $decoded = json_decode($data['details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['details' => 'Format detail tidak valid.']);
            }
            $data['details'] = $decoded;
        }

        // ✅ Validasi dasar
        $validator = \Validator::make($data, [
            'invoice_number'  => 'nullable|string|max:255',
            'customer_name'   => 'nullable|string|max:255',
            'note'            => 'nullable|string|max:1000',
            'payment_method'  => 'required|string|in:cash,transfer,cash_transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'transfer_amount' => 'nullable|numeric|min:0',
            'cash_amount'     => 'nullable|numeric|min:0',
            'reference_no'    => 'nullable|string|max:255',
            'details'         => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.karat_id'   => 'required|exists:karats,id',
            'details.*.gram'         => 'required|numeric|min:0.001',
            'details.*.harga_beli'   => 'nullable|numeric|min:0',
            'details.*.harga_jual'   => 'nullable|numeric|min:0',
        ], [
            'details.required' => 'Minimal satu barang harus diisi.',
        ]);

        // Tambah validasi foto jika penjualan
        if ($type === 'penjualan') {
            $validator->addRules([
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        // 🔹 Upload foto baru jika ada (penjualan)
        if ($type === 'penjualan' && $request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/transaction_photos');
            $validated['photo'] = str_replace('public/', 'storage/', $path);
        }

        // 🔸 Validasi metode pembayaran
        if ($validated['payment_method'] === 'transfer') {
            if (empty($validated['bank_account_id'])) {
                return back()->withInput()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk transfer.']);
            }
            $validated['cash_amount'] = 0;
        }

        if ($validated['payment_method'] === 'cash') {
            $validated['transfer_amount'] = 0;
            $validated['bank_account_id'] = null;
        }

        if ($validated['payment_method'] === 'cash_transfer') {
            if (
                (empty($validated['cash_amount']) && empty($validated['transfer_amount'])) ||
                (($validated['cash_amount'] ?? 0) + ($validated['transfer_amount'] ?? 0)) <= 0
            ) {
                return back()->withInput()->withErrors(['cash_amount' => 'Nominal tunai & transfer wajib diisi.']);
            }

            if (empty($validated['bank_account_id'])) {
                return back()->withInput()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk kombinasi.']);
            }
        }

        try {
            DB::transaction(function () use ($validated, $type, $purchaseType, $id) {

                $transaction = \App\Models\Transaction::findOrFail($id);

                // 🔄 Kembalikan stok lama terlebih dahulu
                foreach ($transaction->details as $oldDetail) {
                    $movementType = $transaction->type === 'purchase' ? 'out' : 'in'; // kebalikan dari store
                    \App\Helpers\StockHelper::moveStock(
                        $oldDetail->product_id,
                        $oldDetail->karat_id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        $movementType,
                        1,
                        $oldDetail->gram,
                        'Transaction',
                        $transaction->id,
                        'rollback-transaction',
                        auth()->id(),
                        $oldDetail->type
                    );
                }

                // 🔥 Hapus detail lama
                $transaction->details()->delete();

                // 🔁 Update header transaksi
                $transaction->update([
                    'invoice_number'   => $validated['invoice_number'] ?? null,
                    'customer_name'    => $validated['customer_name'] ?? null,
                    'note'             => $validated['note'] ?? null,
                    'payment_method'   => $validated['payment_method'],
                    'bank_account_id'  => $validated['bank_account_id'] ?? null,
                    'transfer_amount'  => $validated['transfer_amount'] ?? 0,
                    'cash_amount'      => $validated['cash_amount'] ?? 0,
                    'reference_no'     => $validated['reference_no'] ?? null,
                    'photo'            => $validated['photo'] ?? $transaction->photo,
                    'updated_by'       => auth()->id(),
                ]);

                // 🔄 Tambahkan kembali detail & stok baru
                $total = 0;

                foreach ($validated['details'] as $detail) {
                    $product = \App\Models\Product::findOrFail($detail['product_id']);
                    $karat   = \App\Models\Karat::findOrFail($detail['karat_id']);
                    $gram    = (float) $detail['gram'];

                    $price = $type === 'penjualan'
                        ? (float) ($detail['harga_jual'] ?? 0)
                        : (float) ($detail['harga_beli'] ?? 0);

                    $qty = 1;
                    $subtotal = $price;
                    $total += $subtotal;

                    $goldType = $purchaseType === 'sepuh'
                        ? 'sepuh'
                        : ($purchaseType === 'pabrik' ? 'new' : 'rosok');

                    \App\Models\TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $product->id,
                        'karat_id'       => $karat->id,
                        'gram'           => $gram,
                        'unit_price'     => $price,
                        'type'           => $goldType,
                        'note'           => $detail['note'] ?? null,
                    ]);

                    $movementType = $transaction->type === 'purchase' ? 'in' : 'out';

                    \App\Helpers\StockHelper::moveStock(
                        $product->id,
                        $karat->id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        $movementType,
                        $qty,
                        $gram,
                        'Transaction',
                        $transaction->id,
                        'update-transaction',
                        auth()->id(),
                        $goldType
                    );
                }


                $transaction->update(['total' => $total]);
            });

            return redirect()
                ->route('transaksi.index', ['type' => $type, 'purchaseType' => $purchaseType])
                ->with('status', 'updated');
        } catch (\Throwable $e) {
            \Log::error('Gagal memperbarui transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['msg' => 'Update gagal: ' . $e->getMessage()]);
        }
    }



    public function destroy($type, $purchaseType, Transaction $transaction)
    {
        try {
            DB::transaction(function () use ($transaction, $purchaseType) {
                // rollback semua stok
                foreach ($transaction->details as $detail) {
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_id,
                        $detail->karat_id,
                        2,
                        1,
                        'out',
                        1,
                        $detail->gram ?? null,
                        'Transaction',
                        $transaction->id,
                        "deleted",
                        Auth::id(),
                        $purchaseType
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
