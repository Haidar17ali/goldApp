<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PayrollController extends Controller
{
    public function index()
    {
        $months = Payroll::select(
            DB::raw("DATE_FORMAT(periode, '%Y-%m') as bulan"),
            DB::raw("MIN(DATE_FORMAT(periode, '%M %Y')) as bulan_nama"),
            DB::raw("COUNT(DISTINCT user_id) as jumlah_karyawan"),
            DB::raw("SUM(gaji) as total_gaji"),
            DB::raw("SUM(bonus) as total_bonus"),
            DB::raw("SUM(potongan) as total_potongan")
        )
            ->groupBy(DB::raw("DATE_FORMAT(periode, '%Y-%m')"))
            ->orderByDesc(DB::raw("DATE_FORMAT(periode, '%Y-%m')"))
            ->get();

        return view('pages.payroll.index', compact('months'));
    }

    public function generate(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        $users = User::where('is_active', 1)->get();

        return view('pages.payroll.create', compact(
            'users',
            'month',
            'year',
            'daysInMonth'
        ));
    }

    public function storeGenerate(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $periode = Carbon::create($year, $month, 1);

        foreach ($request->data as $row) {

            Payroll::create([
                'user_id'     => $row['user_id'],
                'gaji'        => $row['gaji'],
                'bonus'       => $row['bonus'],
                'potongan'    => $row['potongan'],
                'hari_kerja'  => $row['hari_kerja'],
                'sistem_gaji' => 'tf',
                'periode'     => $periode,
            ]);
        }

        return redirect()
            ->route('payroll.index')
            ->with('status', 'saved!');
    }
}
