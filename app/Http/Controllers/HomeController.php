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
                DB::raw('SUM(stocks.quantity * pv.gram) as total_gram'),
                DB::raw('SUM(stocks.quantity) as total_qty')
            )
            ->groupBy(
                'pv.product_id',
                'p.name',
                'pv.karat_id',
                'k.name'
            )
            ->get();

        // penjualan
        // ===============================
        // DATE RANGE (DEFAULT HARI INI)
        // ===============================
        $startDate = $request->start_date ?? Carbon::today()->toDateString();
        $endDate   = $request->end_date ?? Carbon::today()->toDateString();

        $startHour = $request->start_hour ?? '00:00';
        $endHour   = $request->end_hour ?? '23:59';

        $startDateTime = Carbon::parse($startDate . ' ' . $startHour)->startOfMinute();
        $endDateTime   = Carbon::parse($endDate . ' ' . $endHour)->endOfMinute();


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
            ->whereBetween('transactions.created_at', [$startDateTime, $endDateTime])
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

        // masuk etalase
        $emasMasukEtalase = DB::table('gold_conversion_outputs as gco')
            ->join('gold_conversions as gc', 'gc.id', '=', 'gco.gold_conversion_id')
            ->join('product_variants as pv', 'pv.id', '=', 'gco.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('karats as k', 'k.id', '=', 'pv.karat_id')
            ->whereBetween(DB::raw('DATE(gc.created_at)'), [$startDate, $endDate])
            ->select(
                'p.name as product_name',
                'k.name as karat',
                DB::raw('COUNT(gco.id) as qty'),
                DB::raw('SUM(gco.weight) as total_gram')
            )
            ->groupBy(
                'p.name',
                'k.name'
            )
            ->orderBy('p.name')
            ->get();

        /**
         * GRAND TOTAL GRAM
         */
        $totalEmasMasuk = $emasMasukEtalase->sum('total_gram');

        /**
         * ===============================
         * EMAS KELUAR ETALASE
         * ===============================
         */
        $emasKeluarEtalase = DB::table('gold_merge_conversion_inputs as gmci')
            ->join('gold_merge_conversions as gmc', 'gmc.id', '=', 'gmci.gold_merge_conversion_id')
            ->join('product_variants as pv', 'pv.id', '=', 'gmci.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('karats as k', 'k.id', '=', 'pv.karat_id')
            ->whereBetween(DB::raw('DATE(gmc.created_at)'), [$startDate, $endDate])
            ->select(
                'p.name as product_name',
                'k.name as karat',
                DB::raw('SUM(gmci.qty) as qty'),
                DB::raw('SUM(gmci.qty * pv.gram) as total_gram')
            )
            ->groupBy(
                'p.name',
                'k.name'
            )
            ->orderBy('p.name')
            ->get();

        /**
         * TOTAL EMAS KELUAR
         */
        $totalEmasKeluar = $emasKeluarEtalase->sum('total_gram');


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

        /**
         * ===============================
         * PENJUALAN PER ANAK (CASH & TRANSFER)
         * ===============================
         */
        $salesByEmployeePayment = Transaction::query()
            ->join('users', 'users.id', '=', 'transactions.created_by')
            ->where('transactions.type', 'penjualan')
            ->whereBetween('transactions.created_at', [$startDateTime, $endDateTime])
            ->select([
                'users.id',
                'users.username as employee_name',
                DB::raw('COUNT(transactions.id) as total_transactions'),
                DB::raw('SUM(COALESCE(transactions.cash_amount,0)) as total_cash'),
                DB::raw('SUM(COALESCE(transactions.transfer_amount,0)) as total_transfer'),
                DB::raw('SUM(COALESCE(transactions.cash_amount,0) + COALESCE(transactions.transfer_amount,0)) as total_setoran')
            ])
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_setoran')
            ->get();



        return view('home', compact([
            'salesByProduct',
            'salesByEmployeePayment',
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
            'emasMasukEtalase',
            'totalEmasMasuk',
            'emasKeluarEtalase',
            'totalEmasKeluar',


        ]));
    }
}
