<?php

namespace App\Http\Controllers;

use App\Helpers\StockHelper;
use App\Models\BankAccount;
use App\Models\CustomerSupplier;
use App\Models\Karat;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesController extends BaseController
{
    public function index($type)
    {
        return view('pages.sales.index', compact("type"));
    }

    protected function generateUniqueInvoiceNumber()
    {
        do {
            $invoice = 'INV-' . strtoupper(Str::random(6));
        } while (Transaction::where('invoice_number', $invoice)->exists());

        return $invoice;
    }

    public function create($type)
    {
        $bankAccounts = BankAccount::orderBy("id", "desc")->get();
        $invoiceNumber = $this->generateUniqueInvoiceNumber();

        // $products = Product::orderBy('name')->pluck('name')->toArray();
        // $karats = Karat::orderBy('name')->pluck('name')->toArray();
        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
            'stocks' => function ($q) {
                $q->where('quantity', '>', 0);
            }
        ])->where("gram", "!=", null)->get();
        $customers = CustomerSupplier::orderBy("id", "desc")->get();

        return view('pages.sales.create', compact('invoiceNumber', 'bankAccounts', "type", "customers", "productVariants"));
    }

    public function store(Request $request, $type)
    {
        // ================= VALIDASI =================
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',

            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',

            'payment_method' => 'required|in:cash,transfer,cash_transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'cash_amount' => 'nullable|numeric|min:0',
            'transfer_amount' => 'nullable|numeric|min:0',

            'details' => 'required|array|min:1',
            'details.*.variant_id' => 'required|exists:product_variants,id',
            'details.*.harga_jual' => 'required|numeric|min:0',

            'photo_base64' => 'nullable',
        ]);

        // ================= FOTO =================
        $photo = null;
        if ($request->photo_base64) {
            @list(, $fileData) = explode(',', $request->photo_base64);
            if ($fileData) {
                $fileName = 'sales_' . time() . '.png';
                $path = public_path('assets/images/penjualan');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                file_put_contents($path . '/' . $fileName, base64_decode($fileData));
                $photo = 'assets/images/penjualan/' . $fileName;
            }
        }

        // ================= HITUNG TOTAL =================
        $total = collect($validated['details'])
            ->sum(fn($d) => (float) $d['harga_jual']);

        // ================= NORMALISASI PEMBAYARAN =================
        $cash = (float) ($validated['cash_amount'] ?? 0);
        $transfer = (float) ($validated['transfer_amount'] ?? 0);

        if ($validated['payment_method'] === 'cash') {
            $cash = $total;
            $transfer = 0;
            $validated['bank_account_id'] = null;
        }

        if ($validated['payment_method'] === 'transfer') {
            if (!$validated['bank_account_id']) {
                return back()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk transfer']);
            }
            $cash = 0;
            $transfer = $total;
        }

        if ($validated['payment_method'] === 'cash_transfer') {
            if (!$validated['bank_account_id']) {
                return back()->withErrors(['bank_account_id' => 'Rekening wajib diisi']);
            }
            if (($cash + $transfer) != $total) {
                return back()->withErrors([
                    'cash_amount' => 'Total tunai + transfer harus sama dengan total transaksi'
                ]);
            }
        }

        // ================= SIMPAN =================
        DB::beginTransaction();
        try {

            // 🔹 CUSTOMER
            $customer = null;
            if (!empty($validated['customer_name'])) {
                $customer = CustomerSupplier::firstOrCreate(
                    ['name' => trim($validated['customer_name'])],
                    [
                        'phone_number' => $validated['customer_phone'],
                        'address' => $validated['customer_address'],
                        'type' => 'customer',
                    ]
                );
            }

            // 🔹 TRANSACTION
            $transaction = Transaction::create([
                'type' => 'penjualan',
                'purchase_type' => 'new',
                'branch_id' => 2,
                'storage_location_id' => 1,
                'transaction_date' => now(),

                'invoice_number' => $validated['invoice_number'],
                'customer_id' => $customer?->id,
                'note' => $validated['note'],
                'photo' => $photo,

                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'],
                'cash_amount' => $cash,
                'transfer_amount' => $transfer,

                'total' => $total,
                'created_by' => auth()->id(),
            ]);

            // 🔹 DETAIL + STOCK MOVEMENT
            foreach ($validated['details'] as $detail) {

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $detail['variant_id'],
                    'unit_price' => $detail['harga_jual'],
                    'type' => 'new',
                ]);

                // === STOCK KELUAR ===
                StockHelper::stockOut(
                    $detail['variant_id'],
                    $transaction->branch_id,
                    $transaction->storage_location_id,
                    1, // qty
                    null,
                    'Transaction',
                    $transaction->id,
                    'Penjualan',
                    auth()->id(),
                    'new'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_print' => route('penjualan.cetak', $transaction->id),
                'redirect_index' => route('penjualan.index', $type),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Gagal simpan penjualan', ['error' => $e]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function print($id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);
        return view('pages.sales.print', compact('transaction'));
    }

    public function edit($type, $id)
    {
        $transaction = Transaction::with([
            'details.productVariant.product',
            'details.productVariant.karat',
            'customer'
        ])->findOrFail($id);

        $bankAccounts = BankAccount::orderBy("id", "desc")->get();

        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
            'stocks' => function ($q) {
                $q->where('quantity', '>', 0);
            }
        ])->get();

        $customers = CustomerSupplier::orderBy("id", "desc")->get();

        // === FORMAT DETAIL UNTUK FRONTEND (SAMA SEPERTI ADD ITEM)
        $details = $transaction->details->map(function ($d) {
            return [
                'id' => $d->product_variant_id,
                'barcode' => $d->productVariant->barcode,
                'sku' => $d->productVariant->sku,
                'gram' => $d->productVariant->gram,
                'default_price' => $d->unit_price,
                'product' => [
                    'name' => $d->productVariant->product->name ?? '',
                ],
                'karat' => [
                    'name' => $d->productVariant->karat->name ?? '',
                ],
            ];
        });

        return view('pages.sales.edit', compact(
            'transaction',
            'bankAccounts',
            'productVariants',
            'details',
            'type',
            'customers'
        ));
    }


    public function update(Request $request, $type, $id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);
        $photo = $transaction->photo;

        /* ================= VALIDASI ================= */
        $validated = $request->validate([
            'invoice_number' => 'nullable|string|max:255|unique:transactions,invoice_number,' . $transaction->id,

            'customer_name' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',

            'payment_method' => 'required|in:cash,transfer,cash_transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'cash_amount' => 'nullable|numeric|min:0',
            'transfer_amount' => 'nullable|numeric|min:0',
            // 'reference_no' => 'nullable|string|max:255',

            'details' => 'required|array|min:1',
            'details.*.variant_id' => 'required|exists:product_variants,id',
            'details.*.harga_jual' => 'required|numeric|min:0',

            'photo_base64' => 'nullable',
        ]);

        /* ================= FOTO ================= */
        if ($request->photo_base64) {
            @list(, $fileData) = explode(',', $request->photo_base64);
            if ($fileData) {
                $fileName = 'sales_' . time() . '.png';
                $path = public_path('assets/images/penjualan');

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                file_put_contents($path . '/' . $fileName, base64_decode($fileData));
                $photo = 'assets/images/penjualan/' . $fileName;
            }
        }

        /* ================= HITUNG TOTAL ================= */
        $total = collect($validated['details'])
            ->sum(fn($d) => (float) $d['harga_jual']);

        /* ================= NORMALISASI PEMBAYARAN ================= */
        $cash = (float) ($validated['cash_amount'] ?? 0);
        $transfer = (float) ($validated['transfer_amount'] ?? 0);

        if ($validated['payment_method'] === 'cash') {
            $cash = $total;
            $transfer = 0;
            $validated['bank_account_id'] = null;
        }

        if ($validated['payment_method'] === 'transfer') {
            if (!$validated['bank_account_id']) {
                return back()->withErrors(['bank_account_id' => 'Rekening wajib diisi']);
            }
            $cash = 0;
            $transfer = $total;
        }

        if ($validated['payment_method'] === 'cash_transfer') {
            if (!$validated['bank_account_id']) {
                return back()->withErrors(['bank_account_id' => 'Rekening wajib diisi']);
            }
            if (($cash + $transfer) != $total) {
                return back()->withErrors([
                    'cash_amount' => 'Total tunai + transfer harus sama dengan total transaksi'
                ]);
            }
        }

        /* ================= UPDATE TRANSAKSI ================= */
        DB::beginTransaction();
        try {

            /* === ROLLBACK STOK LAMA === */
            foreach ($transaction->details as $oldDetail) {
                StockHelper::stockIn(
                    $oldDetail->product_variant_id,
                    $transaction->branch_id,
                    $transaction->storage_location_id,
                    1,
                    null,
                    'Transaction',
                    $transaction->id,
                    'Rollback Update Penjualan',
                    auth()->id(),
                    $oldDetail->type
                );
            }

            /* === HAPUS DETAIL LAMA === */
            $transaction->details()->delete();

            /* === UPDATE HEADER === */
            $transaction->update([
                'invoice_number' => $validated['invoice_number'],
                'customer_name' => $validated['customer_name'],
                'note' => $validated['note'],
                'photo' => $photo,

                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'],
                'cash_amount' => $cash,
                'transfer_amount' => $transfer,
                // 'reference_no' => $validated['reference_no'],

                'total' => $total,
                'updated_by' => auth()->id(),
            ]);

            /* === SIMPAN DETAIL BARU + STOCK OUT === */
            foreach ($validated['details'] as $detail) {

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $detail['variant_id'],
                    'unit_price' => $detail['harga_jual'],
                    'type' => 'new',
                ]);

                StockHelper::stockOut(
                    $detail['variant_id'],
                    $transaction->branch_id,
                    $transaction->storage_location_id,
                    1,
                    null,
                    'Transaction',
                    $transaction->id,
                    'Update Penjualan',
                    auth()->id(),
                    'new'
                );
            }

            DB::commit();

            return redirect()
                ->route('penjualan.index', $type)
                ->with('status', 'Transaksi berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Update penjualan gagal', ['error' => $e]);

            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function destroy($type, $id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);

        try {
            DB::transaction(function () use ($transaction) {

                /* === ROLLBACK STOK === */
                foreach ($transaction->details as $detail) {

                    StockHelper::stockIn(
                        $detail->product_variant_id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        1, // qty
                        null,
                        'Transaction',
                        $transaction->id,
                        'Delete Penjualan',
                        auth()->id(),
                        $detail->type ?? 'new'
                    );
                }

                /* === HAPUS DETAIL === */
                $transaction->details()->delete();

                /* === HAPUS HEADER === */
                $transaction->delete();
            });

            return redirect()
                ->route('penjualan.index', ['type' => $type])
                ->with('status', 'Transaksi berhasil dihapus');
        } catch (\Throwable $e) {
            \Log::error('Gagal hapus transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'msg' => 'Hapus gagal: ' . $e->getMessage()
            ]);
        }
    }
}
