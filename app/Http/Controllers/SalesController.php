<?php

namespace App\Http\Controllers;

use App\Helpers\StockHelper;
use App\Models\BankAccount;
use App\Models\Karat;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesController extends Controller
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

        $products = Product::orderBy('name')->pluck('name')->toArray();
        $karats = Karat::orderBy('name')->pluck('name')->toArray();

        return view('pages.sales.create', compact('invoiceNumber', 'products', 'karats', 'bankAccounts', "type"));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $photo = "";

        // Validasi
        $validated = $request->validate([
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
            'details.*.harga_jual'   => 'required|numeric|min:0',
            'photo_base64'           => 'nullable',
        ]);
        
        if ($request->photo_base64) {
            $image = $request->photo_base64;

            // Ambil bagian base64 setelah koma
            @list($type, $fileData) = explode(';', $image);
            @list(, $fileData) = explode(',', $fileData);

            if ($fileData != "") {
                $fileData = base64_decode($fileData);

                $fileName = 'sales_' . time() . '.png';
                $folder = public_path('assets/images/penjualan');

                // Pastikan folder ada
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                $filePath = $folder . '/' . $fileName;

                file_put_contents($filePath, $fileData);

                // Simpan path ke database (relatif ke public)
                $photo = 'assets/images/penjualan/' . $fileName;
            }
        }


        // Validasi metode pembayaran
        if ($validated['payment_method'] === 'transfer' && empty($validated['bank_account_id'])) {
            return back()->withInput()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk transfer.']);
        }

        if ($validated['payment_method'] === 'cash') {
            $validated['transfer_amount'] = 0;
            $validated['bank_account_id'] = null;
        }

        if ($validated['payment_method'] === 'cash_transfer') {
            if (($validated['cash_amount'] ?? 0) + ($validated['transfer_amount'] ?? 0) <= 0) {
                return back()->withInput()->withErrors(['cash_amount' => 'Nominal tunai & transfer wajib diisi.']);
            }
            if (empty($validated['bank_account_id'])) {
                return back()->withInput()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk kombinasi.']);
            }
        }

        // Simpan transaksi
        try {
            DB::transaction(function () use ($validated, $photo) {
                $transaction = Transaction::create([
                    'type' => 'penjualan',
                    'purchase_type' => 'new', // bisa set default
                    'branch_id' => 2,
                    'storage_location_id' => 1,
                    'transaction_date' => now(),
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'created_by' => auth()->id(),
                    'total' => 0,
                    'photo' => $photo ?? null,
                    'payment_method' => $validated['payment_method'],
                    'bank_account_id' => $validated['bank_account_id'] ?? null,
                    'transfer_amount' => $validated['transfer_amount'] ?? 0,
                    'cash_amount' => $validated['cash_amount'] ?? 0,
                    'reference_no' => $validated['reference_no'] ?? null,
                ]);

                $total = 0;

                foreach ($validated['details'] as $detail) {
                    $product = Product::firstOrCreate(
                        ['name' => trim($detail['product_name'])],
                        ['code' => Str::slug($detail['product_name'])]
                    );
                    $karat = Karat::firstOrCreate(['name' => trim($detail['karat_name'])]);

                    $price = (float) $detail['harga_jual'];
                    $gram = (float) $detail['gram'];
                    $subtotal = $price;
                    $total += $subtotal;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'karat_id' => $karat->id,
                        'gram' => $gram,
                        'unit_price' => $price,
                        'type' => 'new',
                    ]);

                    // kurangi stok
                    StockHelper::moveStock(
                        $product->id,
                        $karat->id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        'out',
                        1,
                        $gram,
                        'Transaction',
                        $transaction->id,
                        'create-sale',
                        auth()->id(),
                        "new"
                    );
                }

                $transaction->update(['total' => $total]);
            });

            return redirect()->route('penjualan.index', "penjualan")->with('status', 'Transaksi penjualan berhasil disimpan.');
        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan penjualan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->withErrors(['msg' => 'Transaksi gagal: ' . $e->getMessage()]);
        }
    }

    public function destroy($type, $id)
    {
        $transaction = Transaction::findOrFail($id);
        try {
            DB::transaction(function () use ($transaction, ) {
                // rollback semua stok
                foreach ($transaction->details as $detail) {
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_id,
                        $detail->karat_id,
                        2,
                        1,
                        'in',
                        1,
                        $detail->gram ?? null,
                        'Transaction',
                        $transaction->id,
                        "deleted",
                        Auth::id(),
                        "new"
                    );
                }

                // hapus detail
                $transaction->details()->delete();

                // hapus header
                $transaction->delete();
            });

            return redirect()->route('transaksi.index', ['type' => $type])->with('status', 'deleted');
        } catch (\Throwable $e) {
            \Log::error('Gagal hapus transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['msg' => 'Hapus gagal: ' . $e->getMessage()]);
        }
    }
}
