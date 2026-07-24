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
use App\Helpers\OnlineSaleHelper;
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
        // dd($marketplace);

        return view(
            'pages.online-sales.index',
            compact(
                'transactions',
                'marketplace',
                'statistics',
                "id"
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

        try {

            $transaction = OnlineSaleHelper::store(
                $validated,
                $request,
                $marketplaceId
            );

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

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }


    public function edit($olshopId, $id)
    {
        $branchId = auth()->user()->profile->branch_id;

        $transaction = Transaction::with([
            'details.productVariant.product',
            'details.productVariant.karat',
            'customer',
            'bank',
            'transactionMarketplace.marketplace',
        ])->findOrFail($id);

        $bankAccounts = BankAccount::where('branch_id', $branchId)
            ->orderByDesc('id')
            ->get();

        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
            'stocks' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }
        ])
            ->whereHas('stocks', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereNotNull('gram')
            ->get();

        $customers = CustomerSupplier::orderByDesc('id')->get();

        return view('pages.online-sales.create', [
            'transaction'     => $transaction,
            'olshopId'        => $olshopId,
            'bankAccounts'    => $bankAccounts,
            'customers'       => $customers,
            'productVariants' => $productVariants,
        ]);
    }

    public function update(Request $request, $marketplaceId, $id)
    {
        $validated = $request->validate([

            'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number,' . $id,

            'customer_name'    => 'nullable|string|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'note'             => 'nullable|string',

            'marketplace_total' => 'required|numeric|min:0',
            'received_amount'   => 'required|numeric|min:0',

            'details' => 'required|array|min:1',
            'details.*.variant_id' => 'required|exists:product_variants,id',
            'details.*.harga_jual' => 'required|numeric|min:0',

            'manik_price' => 'nullable|numeric|min:0',

            'photo_base64' => 'nullable',
        ]);

        try {

            $transaction = OnlineSaleHelper::update(

                transactionId: $id,

                validated: $validated,

                request: $request,

                marketplaceId: $marketplaceId

            );

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

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }

    public function destroy($marketplaceId, $id)
    {
        try {

            OnlineSaleHelper::destroy($id);

            return response()->json([

                'success' => true,

                'message' => 'Penjualan online berhasil dihapus.'

            ]);
        } catch (\Throwable $e) {

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }

    public function kasOnline(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:transactions,id'],
        ]);

        DB::transaction(function () use ($request) {

            $transactions = Transaction::with('transactionMarketplace')
                ->whereIn('id', $request->ids)
                ->get();

            foreach ($transactions as $transaction) {

                // Ambil data marketplace dari relasi
                $marketplace = $transaction->transactionMarketplace;

                // Tidak ada data marketplace
                if (!$marketplace) {
                    continue;
                }

                // Sudah success
                if ($marketplace->payment_status === 'success') {
                    continue;
                }

                // Nominal tidak valid
                if ($marketplace->received_amount <= 0) {
                    continue;
                }

                $journal = Journal::where('source_type', TransactionMarketplace::class)
                    ->where('source_id', $marketplace->id)
                    ->latest()
                    ->first();

                if (
                    $journal &&
                    stripos($journal->description, '[HAPUS]') === false
                ) {
                    continue;
                }

                // Update status pembayaran marketplace
                $marketplace->update([
                    'payment_status' => 'sukses',
                    'settlement_at'  => now(),
                ]);

                // Posting jurnal
                AccountingHelper::post([

                    'date' => now(),

                    'reference' => $transaction->invoice_number,

                    'description' => 'Saldo Masuk Kas Online ',

                    'source_type' => TransactionMarketplace::class,

                    'source_id' => $marketplace->id,

                    'lines' => [

                        [
                            'account' => '101.00.12', // Kas Online
                            'debit' => $marketplace->received_amount,
                            'credit' => 0,
                            'description' => 'Kas Online',
                        ],

                        [
                            'account' => '102.00.01', // Piutang Online
                            'debit' => 0,
                            'credit' => $marketplace->received_amount,
                            'description' => 'Piutang Online',
                        ],

                    ]

                ]);
            }
        });

        return back()->with('success', 'Kas Online berhasil diproses.');
    }
}
