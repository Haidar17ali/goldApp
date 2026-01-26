<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $users = User::all();

        $stocks = Stock::query()
            ->join('product_variants as pv', 'pv.id', '=', 'stocks.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('karats as k', 'k.id', '=', 'pv.karat_id')
            ->where('p.name', '!=', 'emas')
            ->select(
                'pv.product_id',
                'p.name as product_name',
                'pv.karat_id',
                'k.name as karat_name',
                DB::raw('SUM(stocks.quantity * pv.gram) as total_gram')
            )
            ->groupBy(
                'pv.product_id',
                'p.name',
                'pv.karat_id',
                'k.name'
            )
            ->get();


        // $emasId = Product::where('name', 'emas')->value('id');

        // $stockBrankas = Stock::query()
        //     ->join('product_variants as pv', 'pv.id', '=', 'stocks.product_variant_id')
        //     ->join('products as p', 'p.id', '=', 'pv.product_id')
        //     ->join('karats as k', 'k.id', '=', 'pv.karat_id')
        //     ->where('pv.product_id', $emasId)
        //     ->select(
        //         'pv.product_id',
        //         'pv.type',
        //         'p.name as product_name',
        //         'pv.karat_id',
        //         'k.name as karat_name',
        //         'pv.type as variant_type',
        //         DB::raw('SUM(pv.gram) as total_gram')
        //     )
        //     ->groupBy(
        //         'pv.product_id',
        //         'p.name',
        //         'pv.karat_id',
        //         'k.name',
        //         'pv.type'
        //     )
        //     ->get();

        // penjualan
        // ===============================
        // DATE RANGE (DEFAULT HARI INI)
        // ===============================
        $startDate = $request->start_date ?? Carbon::today()->toDateString();
        $endDate   = $request->end_date ?? Carbon::today()->toDateString();

        /**
         * ===============================
         * PENJUALAN GROUP PRODUCT + KARAT
         * ===============================
         */
        $salesByProduct = TransactionDetail::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('product_variants', 'product_variants.id', '=', 'transaction_details.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('karats', 'karats.id', '=', 'product_variants.karat_id')
            ->where('transactions.type', 'penjualan')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->select([
                'products.name as product_name',
                'karats.name as karat',
                DB::raw('COUNT(transaction_details.id) as qty'),
                DB::raw('SUM(product_variants.gram) as total_gram'),
                DB::raw('SUM(transaction_details.unit_price) as total_nominal'),
            ])
            ->groupBy('products.name', 'karats.name')
            ->orderBy('products.name')
            ->get();

        /**
         * ===============================
         * TOTAL CASH
         * ===============================
         */
        $cashTotal = Transaction::where('type', 'penjualan')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('cash_amount');

        /**
         * ===============================
         * TRANSFER PER BANK
         * ===============================
         */
        $transferByBank = Transaction::query()
            ->join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->where('transactions.type', 'penjualan')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->whereNotNull('transactions.transfer_amount')
            ->select([
                'bank_accounts.bank_name',
                DB::raw('SUM(transactions.transfer_amount) as total_transfer')
            ])
            ->groupBy('bank_accounts.bank_name')
            ->get();

        /**
         * ===============================
         * GRAND TOTAL
         * ===============================
         */
        $grandTotal = $salesByProduct->sum('total_nominal');

        // performa karyawan
        $employeePerformance = TransactionDetail::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('users', 'users.id', '=', 'transactions.created_by')
            ->join('product_variants', 'product_variants.id', '=', 'transaction_details.product_variant_id')
            ->where('transactions.type', 'penjualan')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])

            ->select([
                'users.id',
                'users.username as employee_name',
                DB::raw('COUNT(DISTINCT transactions.id) as total_transactions'),
                DB::raw('COUNT(transaction_details.id) as qty'),
                DB::raw('SUM(product_variants.gram) as total_gram'),
                DB::raw('SUM(transaction_details.unit_price) as total_nominal'),
            ])
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_nominal')
            ->get();

        // produk paling laku
        $bestProducts = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('product_variants as pv', 'pv.id', '=', 'td.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('karats as k', 'k.id', '=', 'pv.karat_id')
            ->where('t.type', 'penjualan')
            ->whereBetween('t.transaction_date', [$startDate, $endDate])
            ->groupBy(
                'p.id',
                'p.name',
                'k.name'
            )
            ->select(
                'p.name as product_name',
                'k.name as karat',
                DB::raw('COUNT(td.id) as total_qty'),
                DB::raw('SUM(pv.gram) as total_gram'),
                DB::raw('SUM(td.unit_price) as total_nominal')
            )
            ->orderByDesc('total_qty')
            ->get();

        $jamPalingRamai = DB::table('transactions')
            ->where('type', 'penjualan')
            ->selectRaw("
                HOUR(CONVERT_TZ(created_at, '+00:00', '+07:00')) as jam,
                COUNT(*) as total_transaksi
            ")
            ->groupBy('jam')
            ->orderByDesc('total_transaksi')
            ->first();

        $hariPalingRamai = DB::table('transactions')
            ->where('type', 'penjualan')
            ->selectRaw("
                DAYOFWEEK(CONVERT_TZ(created_at, '+00:00', '+07:00')) as hari_angka,
                DAYNAME(CONVERT_TZ(created_at, '+00:00', '+07:00')) as hari,
                COUNT(*) as total_transaksi
            ")
            ->groupBy('hari_angka', 'hari')
            ->orderByDesc('total_transaksi')
            ->first();

        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return view('home', compact([
            'salesByProduct',
            'cashTotal',
            'transferByBank',
            'grandTotal',
            'startDate',
            'endDate',
            'users',
            'stocks',
            'employeePerformance',
            'bestProducts',
            'jamPalingRamai',
            'hariPalingRamai',
            'hariMap',
        ]));
    }

    // public function filterStok(Request $request){
    //     $startDate = $request->input('start_date');
    //     $lastDate = $request->input('last_date') ?? $startDate;

    //     // LPB terpakai dalam rentang tanggal
    //     $lpbsTerpakai = LPB::where('used', true)
    //         ->whereBetween('used_at', [$startDate, $lastDate])
    //         ->with('details')
    //         ->get();

    //     $paidLpbs = LPB::where("paid_at", "!=", null)
    //         ->whereBetween('paid_at', [$startDate, $lastDate])
    //         ->with('details')
    //         ->get();

    //     $paidLpbData = [
    //         'reject_130' => 0,
    //         'super_130'  => 0,
    //         'super_260'  => 0,
    //     ];

    //     $stokTerpakai = [
    //         'reject_130' => 0,
    //         'super_130'  => 0,
    //         'super_260'  => 0,
    //     ];

    //     foreach ($paidLpbs as $index => $paidLpb) {
    //         foreach ($paidLpb->details as $detail) {
    //             if ($detail->quality == "Afkir" && $detail->length == 130) {
    //                 $paidLpbData["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
    //             } elseif ($detail->quality == "Super") {
    //                 if ($detail->length == 130) {
    //                     $paidLpbData["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
    //                 } elseif ($detail->length == 260) {
    //                     $paidLpbData["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
    //                 }
    //             }
    //         }
    //     }

    //     foreach ($lpbsTerpakai as $index => $lpb) {
    //         foreach ($lpb->details as $detail) {
    //             if ($detail->quality == "Afkir" && $detail->length == 130) {
    //                 $stokTerpakai["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
    //             } elseif ($detail->quality == "Super") {
    //                 if ($detail->length == 130) {
    //                     $stokTerpakai["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
    //                 } elseif ($detail->length == 260) {
    //                     $stokTerpakai["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
    //                 }
    //             }
    //         }
    //     }

    //     // LPB belum terpakai → belum digunakan sampai batas lastDate
    //     $stockGrouped = LpbDetail::whereHas('lpb', function ($query) use ($lastDate) {
    //         $query->where(function ($q) {
    //             $q->whereNull('used')
    //             ->orWhere('used', 0);
    //         })
    //         ->whereDate('date', '<=', $lastDate) // ⬅️ batas terakhir LPB
    //         ->orWhere('used_at', '>', $lastDate);
    //     })
    //     ->select('quality', 'length', 'diameter')
    //     ->selectRaw('SUM(qty) as total_qty')
    //     ->groupBy('quality', 'length', 'diameter')
    //     ->orderBy('quality')
    //     ->orderBy('length')
    //     ->orderBy('diameter')
    //     ->get();

    //     $stokBelumTerpakai = [
    //         'reject_130' => 0,
    //         'super_130'  => 0,
    //         'super_260'  => 0,
    //     ];

    //     foreach ($stockGrouped as $detail) {
    //         if ($detail->quality == "Afkir" && $detail->length == 130) {
    //             $stokBelumTerpakai["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->total_qty);
    //         } elseif ($detail->quality == "Super") {
    //             if ($detail->length == 130) {
    //                 $stokBelumTerpakai["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->total_qty);
    //             } elseif ($detail->length == 260) {
    //                 $stokBelumTerpakai["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->total_qty);
    //             }
    //         }
    //     }


    //     return response()->json([
    //         'stok_terpakai' => $stokTerpakai,
    //         'stok_belum_terpakai' => $stokBelumTerpakai,
    //         'paidLpbData' => $paidLpbData,
    //     ]);
    // }

}
