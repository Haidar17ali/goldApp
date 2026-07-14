<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\Transaction;
use Illuminate\Http\Request;

use App\Helpers\StockHelper;
use App\Models\BankAccount;
use App\Models\CustomerSupplier;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\AccountingHelper;
use App\Helpers\GoldHelper;
use App\Models\Journal;
use App\Models\TransactionDetail;
use App\Models\TransactionMarketplace;;

class OnlineTransactionController extends Controller
{
    public function index(Request $request, $id)
    {
        $marketplace = Marketplace::findOrFail($id);

        $query = Transaction::query()
            ->with([
                'customer',
                'branch',
                'user',
                'transactionMarketplace',
                'transactionMarketplace.marketplace',
                'details.productVariant.product',
                'details.productVariant.karat',
            ])
            ->whereHas('transactionMarketplace', function ($q) use ($marketplace) {
                $q->where('marketplace_id', $marketplace->id);
            });

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('invoice_number', 'like', "%{$search}%")

                    ->orWhereHas('transactionMarketplace', function ($marketplaceQuery) use ($search) {

                        $marketplaceQuery
                            ->where('order_id', 'like', "%{$search}%")
                            ->orWhere('buyer_name', 'like', "%{$search}%")
                            ->orWhere('tracking_number', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status Order
        |--------------------------------------------------------------------------
        */

        if ($request->filled('order_status')) {

            $query->whereHas('transactionMarketplace', function ($q) use ($request) {

                $q->where('order_status', $request->order_status);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Payment Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_status')) {

            $query->whereHas('transactionMarketplace', function ($q) use ($request) {

                $q->where('payment_status', $request->payment_status);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {

            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'orders' => (clone $query)->count(),

            'sales' => (clone $query)->sum('total'),

            'marketplace_total' => (clone $query)
                ->join(
                    'transaction_marketplaces',
                    'transactions.id',
                    '=',
                    'transaction_marketplaces.transaction_id'
                )
                ->sum('transaction_marketplaces.marketplace_total'),

            'received' => (clone $query)
                ->join(
                    'transaction_marketplaces',
                    'transactions.id',
                    '=',
                    'transaction_marketplaces.transaction_id'
                )
                ->sum('transaction_marketplaces.received_amount'),

            'waiting_settlement' => (clone $query)
                ->whereHas('transactionMarketplace', function ($q) {

                    $q->whereNull('settlement_at');
                })
                ->count(),
        ];

        $transactions = $query
            ->latest('transaction_date')
            ->paginate(20)
            ->withQueryString();

        return view(
            'pages.online-sales.index',
            compact(
                'transactions',
                'marketplace',
                'statistics'
            )
        );
    }

    protected function generateUniqueInvoiceNumber()
    {
        do {
            $invoice = 'INV-' . generateUniqueBarcode();
        } while (Transaction::where('invoice_number', $invoice)->exists());

        return $invoice;
    }

    public function create($id)
    {
        $bankAccounts = BankAccount::orderBy("id", "desc")->where("branch_id", Auth::user()->profile->branch_id)->get();
        $invoiceNumber = $this->generateUniqueInvoiceNumber();

        $branchId = auth()->user()->profile->branch_id;

        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
            'stocks' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                    ->where('quantity', '>', 0);
            }
        ])
            ->whereHas('stocks', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                    ->where('quantity', '>', 0);
            })
            ->whereNotNull('gram')
            ->get();

        $customers = CustomerSupplier::orderBy("id", "desc")->get();

        return view('pages.online-sales.create', compact('invoiceNumber', 'bankAccounts', "id", "customers", "productVariants"));
    }

    // public function store(Request $request, $type)
    // {
    //     // ================= VALIDASI =================
    //     $validated = $request->validate([
    //         'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',

    //         'customer_name' => 'nullable|string|max:255',
    //         'customer_phone' => 'nullable|string|max:50',
    //         'customer_address' => 'nullable|string|max:255',
    //         'note' => 'nullable|string|max:1000',

    //         'payment_method' => 'required|in:cash,transfer,cash_transfer',
    //         'bank_account_id' => 'nullable|exists:bank_accounts,id',
    //         'cash_amount' => 'nullable|numeric|min:0',
    //         'transfer_amount' => 'nullable|numeric|min:0',

    //         'details' => 'required|array|min:1',
    //         'details.*.variant_id' => 'required|exists:product_variants,id',
    //         'details.*.harga_jual' => 'required|numeric|min:0',

    //         'photo_base64' => 'nullable',

    //         'manik_price' => 'nullable|numeric|min:0',
    //     ]);

    //     // ================= FOTO =================
    //     $photo = null;
    //     if ($request->photo_base64) {
    //         @list(, $fileData) = explode(',', $request->photo_base64);
    //         if ($fileData) {
    //             $fileName = 'sales_' . time() . '.png';
    //             $path = public_path('assets/images/penjualan');
    //             if (!file_exists($path)) {
    //                 mkdir($path, 0777, true);
    //             }
    //             file_put_contents($path . '/' . $fileName, base64_decode($fileData));
    //             $photo = 'assets/images/penjualan/' . $fileName;
    //         }
    //     }

    //     $manikPrice = (float) ($validated['manik_price'] ?? 0);

    //     // ================= HITUNG TOTAL =================
    //     $total = collect($validated['details'])
    //         ->sum(fn($d) => (float) $d['harga_jual']);

    //     $total += $manikPrice;

    //     // ================= NORMALISASI PEMBAYARAN =================
    //     $cash = (float) ($validated['cash_amount'] ?? 0);
    //     $transfer = (float) ($validated['transfer_amount'] ?? 0);

    //     if ($validated['payment_method'] === 'cash') {
    //         $cash = $total;
    //         $transfer = 0;
    //         $validated['bank_account_id'] = null;
    //     }

    //     if ($validated['payment_method'] === 'transfer') {
    //         if (!$validated['bank_account_id']) {
    //             return back()->withErrors(['bank_account_id' => 'Rekening wajib diisi untuk transfer']);
    //         }
    //         $cash = 0;
    //         $transfer = $total;
    //     }

    //     if ($validated['payment_method'] === 'cash_transfer') {
    //         if (!$validated['bank_account_id']) {
    //             return back()->withErrors(['bank_account_id' => 'Rekening wajib diisi']);
    //         }
    //         if (($cash + $transfer) != $total) {
    //             return back()->withErrors([
    //                 'cash_amount' => 'Total tunai + transfer harus sama dengan total transaksi'
    //             ]);
    //         }
    //     }

    //     // ================= SIMPAN =================
    //     DB::beginTransaction();
    //     try {

    //         // 🔹 CUSTOMER
    //         $customer = null;
    //         if (!empty($validated['customer_name'])) {
    //             $customer = CustomerSupplier::firstOrCreate(
    //                 [
    //                     'name' => trim($validated['customer_name']),
    //                     'address' => $validated['customer_address'],
    //                 ],
    //                 [
    //                     'phone_number' => $validated['customer_phone'],
    //                     'type' => 'customer',
    //                 ]
    //             );
    //         }

    //         $user = auth()->user();


    //         if (!$user->profile || !$user->profile->branch_id) {
    //             throw new \Exception('User belum memiliki branch / profile');
    //         }

    //         // 🔹 TRANSACTION
    //         $transaction = Transaction::create([
    //             'type' => 'penjualan',
    //             'purchase_type' => 'new',
    //             'branch_id' => $user->profile->branch_id,
    //             'storage_location_id' => 1,
    //             'transaction_date' => now(),

    //             'invoice_number' => $validated['invoice_number'],
    //             'customer_id' => $customer?->id,
    //             'note' => $validated['note'],
    //             'photo' => $photo,

    //             'payment_method' => $validated['payment_method'],
    //             'bank_account_id' => $validated['bank_account_id'],
    //             'cash_amount' => $cash,
    //             'transfer_amount' => $transfer,

    //             'total' => $total,
    //             'manik_price' => $manikPrice,
    //             'created_by' => auth()->id(),
    //         ]);

    //         $totalHpp = 0;

    //         // 🔹 DETAIL + STOCK MOVEMENT
    //         foreach ($validated['details'] as $detail) {

    //             $variant = ProductVariant::with('karat')->findOrFail($detail['variant_id']);

    //             // 🔥 ambil harga emas berdasarkan karat
    //             $hargaEmas = GoldHelper::getHargaByKarat($variant->karat_id);

    //             $gram = $variant->gram ?? 0;
    //             $qty = 1; // sistem kamu saat ini

    //             // 🔥 hitung HPP item
    //             $hppItem = $hargaEmas * $gram * $qty;

    //             // 🔥 akumulasi
    //             $totalHpp += $hppItem;

    //             TransactionDetail::create([
    //                 'transaction_id' => $transaction->id,
    //                 'product_variant_id' => $detail['variant_id'],
    //                 'unit_price' => $detail['harga_jual'],
    //                 'type' => $variant->type,
    //             ]);

    //             $branchId = $transaction->branch_id;
    //             // === STOCK KELUAR ===
    //             StockHelper::stockOut(
    //                 $detail['variant_id'],
    //                 $branchId,
    //                 1,
    //                 1, // qty
    //                 null,
    //                 'Transaction',
    //                 $transaction->id,
    //                 'Penjualan',
    //                 auth()->id(),
    //                 $variant->type
    //             );
    //         }


    //         // journal data penjualan

    //         $hppAccounts = [
    //             2 => '502.01.01',
    //             1 => '502.01.02',
    //             3 => '502.01.03',
    //         ];

    //         $hppAccount = $hppAccounts[$branchId] ?? '502.01.00';

    //         $salesAccounts = [
    //             2 => '501.01.002', // paserpan
    //             1 => '501.01.001', // pasuruan
    //             3 => '501.01.003', // sa
    //         ];

    //         $cashAccounts = [
    //             1 => '101.00.01',   // Pasuruan

    //             2 => '101.00.06', // Pasuruan
    //             3 => '101.00.08',  // Sandang Ayu
    //         ];

    //         $persediaanAccounts = [
    //             1 => '103.01.001', // Pasuruan
    //             2 => '103.01.002', // Paserpan
    //             3 => '103.01.003', // Sandang Ayu
    //         ];

    //         $persediaanAccount = $persediaanAccounts[auth()->user()->profile->branch_id ?? 1] ?? '103.00.00';


    //         $cashAccount = $cashAccounts[$branchId] ?? '101.00.00';


    //         $salesAccount = $salesAccounts[$branchId] ?? '501.01.00';

    //         $lines = [];

    //         // ================== KAS ==================
    //         if ($transaction->cash_amount > 0) {
    //             $lines[] = [
    //                 'account' => $cashAccount, // kas tunai
    //                 'debit' => $transaction->cash_amount
    //             ];
    //         }

    //         // ================== TRANSFER ==================
    //         if ($transaction->transfer_amount > 0 && $transaction->bank_account_id) {

    //             $bank = BankAccount::find($transaction->bank_account_id);

    //             if (!$bank || !$bank->account_code) {
    //                 throw new \Exception('Mapping akun bank belum diset');
    //             }

    //             $lines[] = [
    //                 'account' => $bank->account_code, // 🔥 dinamis sesuai bank
    //                 'debit' => $transaction->transfer_amount
    //             ];
    //         }

    //         // ================== HPP ==================
    //         $lines[] = [
    //             'account' => $hppAccount,
    //             'debit' => $totalHpp
    //         ];

    //         // ================== PENJUALAN ==================
    //         $lines[] = [
    //             'account' => $salesAccount,
    //             'credit' => $transaction->total
    //         ];

    //         // ================== PERSEDIAAN ==================
    //         $lines[] = [
    //             'account' => $persediaanAccount,
    //             'credit' => $totalHpp
    //         ];

    //         AccountingHelper::post([
    //             'date' => $transaction->transaction_date,
    //             'reference' => $transaction->invoice_number,
    //             'description' => 'Penjualan emas ' . $transaction->invoice_number,
    //             'source_type' => 'sale',
    //             'source_id' => $transaction->id,
    //             'lines' => $lines
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'redirect_print' => route('penjualan.cetak', $transaction->id),
    //             'redirect_index' => route('penjualan.index', $type),
    //         ]);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         \Log::error('Gagal simpan penjualan', ['error' => $e]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request, $marketplaceId)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',

            'customer_name'    => 'nullable|string|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'note'             => 'nullable|string',

            'marketplace_id'   => 'exists:marketplaces,id',

            // pembayaran marketplace
            'marketplace_total' => 'required|numeric|min:0',
            'received_amount'   => 'required|numeric|min:0',

            'details' => 'required|array|min:1',
            'details.*.variant_id' => 'required|exists:product_variants,id',
            'details.*.harga_jual' => 'required|numeric|min:0',

            'manik_price' => 'nullable|numeric|min:0',

            'photo_base64' => 'nullable',
        ]);

        $photo = null;

        if ($request->photo_base64) {

            @list(, $fileData) = explode(',', $request->photo_base64);

            if ($fileData) {

                $fileName = 'sales_' . time() . '.png';

                $path = public_path('assets/images/penjualan');

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                file_put_contents(
                    $path . '/' . $fileName,
                    base64_decode($fileData)
                );

                $photo = 'assets/images/penjualan/' . $fileName;
            }
        }

        $manikPrice = (float)($validated['manik_price'] ?? 0);

        $total = collect($validated['details'])
            ->sum(fn($item) => $item['harga_jual']);

        $total += $manikPrice;

        // pembayaran marketplace

        $marketplaceTotal = (float)$validated['marketplace_total'];

        $receivedAmount = (float)$validated['received_amount'];

        $change = max(
            0,
            $receivedAmount - $total
        );

        DB::beginTransaction();

        try {
            $customer = null;

            if (!empty($validated['customer_name'])) {

                $customer = CustomerSupplier::firstOrCreate(

                    [
                        'name' => trim($validated['customer_name'])
                    ],

                    [
                        'address'      => $validated['customer_address'],
                        'phone_number' => $validated['customer_phone'],
                        'type'         => 'customer',
                    ]

                );
            }

            $user = auth()->user();

            if (!$user->profile || !$user->profile->branch_id) {

                throw new \Exception('User belum memiliki cabang');
            }

            $branchId = $user->profile->branch_id;

            $transaction = Transaction::create([

                'type' => 'penjualan',

                'purchase_type' => 'new',

                'branch_id' => $branchId,

                'storage_location_id' => 1,

                'transaction_date' => now(),

                'invoice_number' => $validated['invoice_number'],

                'customer_id' => $customer?->id,

                'note' => $validated['note'],

                'photo' => $photo,

                // marketplace
                // 'payment_method' => 'marketplace',

                // 'cash_amount' => $change,

                // 'transfer_amount' => $receivedAmount,

                'total' => $total,

                'manik_price' => $manikPrice,

                'created_by' => auth()->id(),

            ]);

            TransactionMarketplace::create([

                'transaction_id' => $transaction->id,

                'marketplace_id' => $marketplaceId,

                // 'buyer_name' => $validated['customer_name'],

                // 'buyer_phone' => $validated['customer_phone'],

                // 'shipping_address' => $validated['customer_address'],

                'marketplace_total' => $marketplaceTotal,

                'received_amount' => $receivedAmount,

            ]);

            $totalHpp = 0;

            foreach ($validated['details'] as $detail) {

                $variant = ProductVariant::with([
                    'product',
                    'karat'
                ])->findOrFail($detail['variant_id']);

                // ===========================
                // Hitung HPP
                // ===========================

                $hargaEmas = GoldHelper::getHargaByKarat(
                    $variant->karat_id
                );

                $gram = $variant->gram ?? 0;

                $hpp = $hargaEmas * $gram;

                $totalHpp += $hpp;

                // ===========================
                // Simpan Detail
                // ===========================

                TransactionDetail::create([

                    'transaction_id' => $transaction->id,

                    'product_variant_id' => $variant->id,

                    'unit_price' => $detail['harga_jual'],

                    'type' => $variant->type,

                    'note' => null,

                ]);

                // ===========================
                // Kurangi Stock
                // ===========================

                StockHelper::stockOut(

                    variantId: $variant->id,

                    branchId: $branchId,

                    storageLocationId: 1,

                    quantity: 1,

                    weight: $gram,

                    refType: 'Transaction',

                    refId: $transaction->id,

                    note: 'Penjualan Online',

                    userId: auth()->id(),

                    goldType: $variant->type

                );
            }

            // ==========================================
            // MAPPING COA PENJUALAN ONLINE
            // ==========================================

            $receivableAccounts = [
                1 => '102.00.01', // TikTok
                2 => '102.00.02', // Shopee
                3 => '102.00.03', // Tokopedia
            ];

            $salesAccounts = [
                1 => '501.03.001', // TikTok
                2 => '501.03.002', // Shopee
                3 => '501.03.003', // Tokopedia
            ];

            // HPP & Persediaan masih dijadikan satu
            $hppAccount = '502.01.04';
            $persediaanAccount = '103.03.01';

            $receivableAccount = $receivableAccounts[$marketplaceId]
                ?? throw new \Exception('Mapping Piutang Marketplace belum dibuat.');

            $salesAccount = $salesAccounts[$marketplaceId]
                ?? throw new \Exception('Mapping Penjualan Marketplace belum dibuat.');

            $lines = [];

            // ================== PIUTANG ONLINE ==================
            $lines[] = [
                'account' => $receivableAccount,
                'debit'   => $transaction->total,
            ];

            // ================== HPP ==================
            $lines[] = [
                'account' => $hppAccount,
                'debit'   => $totalHpp,
            ];

            // ================== PENJUALAN ==================
            $lines[] = [
                'account' => $salesAccount,
                'credit'  => $transaction->total,
            ];

            // ================== PERSEDIAAN ==================
            $lines[] = [
                'account' => $persediaanAccount,
                'credit'  => $totalHpp,
            ];

            AccountingHelper::post([
                'date'        => $transaction->transaction_date,
                'reference'   => $transaction->invoice_number,
                'description' => 'Penjualan Online ' . $transaction->invoice_number,
                'source_type' => 'sale_online',
                'source_id'   => $transaction->id,
                'lines'       => $lines,
            ]);

            DB::commit();

            return response()->json([

                'success' => true,

                'redirect_print' => route(
                    'penjualan.cetak',
                    $transaction->id
                ),

                'redirect_index' => route(
                    'penjualan.online.index',
                    $marketplaceId
                )

            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }

    public function edit($id)
    {
        $transaction = Transaction::with([
            'details.productVariant.product',
            'details.productVariant.karat',
            'marketplace',
            'bankAccount',
        ])->findOrFail($id);

        $bankAccounts = BankAccount::orderBy('id', 'desc')
            ->where('branch_id', auth()->user()->profile->branch_id)
            ->get();

        $branchId = auth()->user()->profile->branch_id;

        /*
    |--------------------------------------------------------------------------
    | Product Variant
    | Tampilkan stok + barang yang sudah ada di transaksi
    |--------------------------------------------------------------------------
    */

        $variantIds = $transaction->details
            ->pluck('product_variant_id')
            ->toArray();

        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
            'stocks' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }
        ])
            ->where(function ($q) use ($branchId, $variantIds) {

                $q->whereHas('stocks', function ($stock) use ($branchId) {

                    $stock->where('branch_id', $branchId)
                        ->where('quantity', '>', 0);
                });

                /*
            |--------------------------------------------------------------------------
            | Tetap tampilkan barang lama walaupun stok sekarang 0
            |--------------------------------------------------------------------------
            */

                if (!empty($variantIds)) {

                    $q->orWhereIn('id', $variantIds);
                }
            })
            ->whereNotNull('gram')
            ->get();

        $customers = CustomerSupplier::orderBy('id', 'desc')->get();

        return view(
            'pages.online-sales.edit',
            compact(
                'transaction',
                'bankAccounts',
                'customers',
                'productVariants'
            )
        );
    }
}
