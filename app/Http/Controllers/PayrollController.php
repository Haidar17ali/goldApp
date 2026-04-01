<?php

namespace App\Http\Controllers;

use App\Helpers\AccountingHelper;
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
            DB::raw("YEAR(periode) as tahun"),
            DB::raw("MONTH(periode) as bulan"),
            DB::raw("MIN(DATE_FORMAT(periode, '%M %Y')) as bulan_nama"),
            DB::raw("COUNT(DISTINCT user_id) as jumlah_karyawan"),
            DB::raw("SUM(gaji) as total_gaji"),
            DB::raw("SUM(bonus) as total_bonus"),
            DB::raw("SUM(potongan) as total_potongan")
        )
            ->groupBy(DB::raw("YEAR(periode), MONTH(periode)"))
            ->orderByDesc(DB::raw("YEAR(periode)"))
            ->orderByDesc(DB::raw("MONTH(periode)"))
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

    // public function storeGenerate(Request $request)
    // {
    //     $month = $request->month;
    //     $year  = $request->year;

    //     $periode = Carbon::create($year, $month, 1);

    //     foreach ($request->data as $row) {

    //         Payroll::create([
    //             'user_id'     => $row['user_id'],
    //             'gaji'        => $row['gaji'],
    //             'bonus'       => $row['bonus'],
    //             'potongan'    => $row['potongan'],
    //             'hari_kerja'  => $row['hari_kerja'],
    //             'sistem_gaji' => 'tf',
    //             'periode'     => $periode,
    //         ]);
    //     }

    //     return redirect()
    //         ->route('payroll.index')
    //         ->with('status', 'saved!');
    // }

    public function storeGenerate(Request $request)
    {
        DB::beginTransaction();

        try {

            $month = $request->month;
            $year  = $request->year;

            $periode = Carbon::create($year, $month, 1);

            // 🔥 Ambil user yang sudah digaji di periode ini
            $existingUserIds = Payroll::whereYear('periode', $year)
                ->whereMonth('periode', $month)
                ->pluck('user_id')
                ->toArray();

            $totalBebanUmum = 0;
            $totalBebanGaji = 0;
            $totalKasKeluar = 0;

            $skippedUsers = []; // untuk info
            $createdCount = 0;

            foreach ($request->data as $row) {

                // ⛔ Skip jika sudah ada
                if (in_array($row['user_id'], $existingUserIds)) {
                    $skippedUsers[] = $row['user_id'];
                    continue;
                }

                $payroll = Payroll::create([
                    'user_id'     => $row['user_id'],
                    'gaji'        => $row['gaji'],
                    'bonus'       => $row['bonus'],
                    'potongan'    => $row['potongan'],
                    'hari_kerja'  => $row['hari_kerja'],
                    'sistem_gaji' => 'tf',
                    'periode'     => $periode,
                ]);

                $createdCount++;

                $user = $payroll->user;

                $total = ($row['gaji'] + $row['bonus']) - $row['potongan'];

                if ($user->hasRole(['super-admin', 'Freelance'])) {
                    $totalBebanUmum += $total;
                } else {
                    $totalBebanGaji += $total;
                }

                $totalKasKeluar += $total;
            }

            // ❌ Kalau tidak ada yang dibuat
            if ($createdCount === 0) {
                DB::rollBack();

                return redirect()
                    ->route('payroll.index')
                    ->with('error', 'Semua karyawan sudah digaji di periode ini!');
            }

            // 🔥 POST JURNAL
            AccountingHelper::post([
                'date' => now(),
                'reference' => 'PAYROLL-' . $month . '-' . $year,
                'description' => 'Penggajian periode ' . $month . '/' . $year,
                'source_type' => 'payroll',
                'source_id' => null,

                'lines' => array_filter([

                    $totalBebanUmum > 0 ? [
                        'account' => '503.05.00',
                        'debit' => $totalBebanUmum
                    ] : null,

                    $totalBebanGaji > 0 ? [
                        'account' => '502.02.00',
                        'debit' => $totalBebanGaji
                    ] : null,

                    [
                        'account' => '101.00.02',
                        'credit' => $totalKasKeluar
                    ]

                ])
            ]);

            DB::commit();

            // 🔥 OPTIONAL: ambil nama user yang ke skip (biar user friendly)
            $skippedNames = \App\Models\User::whereIn('id', $skippedUsers)
                ->pluck('username')
                ->toArray();

            return redirect()
                ->route('payroll.index')
                ->with('status', 'Payroll berhasil dibuat: ' . $createdCount . ' karyawan')
                ->with(
                    'warning',
                    count($skippedNames) > 0
                        ? 'Dilewati (' . count($skippedNames) . '): ' . implode(', ', $skippedNames)
                        : null
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function detail($year, $month)
    {
        $payrolls = Payroll::with('user.profile')
            ->whereYear('periode', $year)
            ->whereMonth('periode', $month)
            ->get();

        // summary
        $summary = [
            'total_gaji' => $payrolls->sum('gaji'),
            'total_bonus' => $payrolls->sum('bonus'),
            'total_potongan' => $payrolls->sum('potongan'),
            'total_dibayar' => $payrolls->sum(fn($p) => $p->total),
        ];

        $periode = Carbon::create($year, $month, 1);

        return view('pages.payroll.show', compact('payrolls', 'summary', 'periode'));
    }

    public function edit($year, $month)
    {
        $payrolls = Payroll::with('user')
            ->whereYear('periode', $year)
            ->whereMonth('periode', $month)
            ->get();

        if ($payrolls->isEmpty()) {
            return redirect()->route('payroll.index')
                ->with('error', 'Data payroll tidak ditemukan');
        }

        $periode = Carbon::create($year, $month, 1);
        $daysInMonth = $periode->daysInMonth;

        return view('pages.payroll.edit', compact(
            'payrolls',
            'periode',
            'daysInMonth',
            'year',
            'month'
        ));
    }

    public function update(Request $request, $year, $month)
    {
        DB::beginTransaction();

        try {

            $periode = Carbon::create($year, $month, 1);

            // 🔥 Ambil payroll existing
            $payrolls = Payroll::whereYear('periode', $year)
                ->whereMonth('periode', $month)
                ->get()
                ->keyBy('user_id');

            // 🔥 Ambil jurnal lama
            $journal = \App\Models\Journal::where('source_type', 'payroll')
                ->where('reference', 'PAYROLL-' . $month . '-' . $year)
                ->whereNull('reversal_of') // hanya jurnal asli
                ->first();

            // 🔥 Reverse jurnal lama
            if ($journal && !$journal->reversedBy) {
                AccountingHelper::reverse($journal, 'Update Payroll');
            }

            $totalBebanUmum = 0;
            $totalBebanGaji = 0;
            $totalKasKeluar = 0;

            foreach ($request->data as $row) {

                if (!isset($payrolls[$row['user_id']])) continue;

                $payroll = $payrolls[$row['user_id']];

                // 🔥 Update data
                $payroll->update([
                    'gaji'        => $row['gaji'],
                    'bonus'       => $row['bonus'],
                    'potongan'    => $row['potongan'],
                    'hari_kerja'  => $row['hari_kerja'],
                ]);

                $user = $payroll->user;

                $total = ($row['gaji'] + $row['bonus']) - $row['potongan'];

                if ($user->hasRole(['super-admin', 'Freelance'])) {
                    $totalBebanUmum += $total;
                } else {
                    $totalBebanGaji += $total;
                }

                $totalKasKeluar += $total;
            }

            // 🔥 POST JURNAL BARU
            AccountingHelper::post([
                'date' => now(),
                'reference' => 'PAYROLL-' . $month . '-' . $year,
                'description' => 'Update Penggajian periode ' . $month . '/' . $year,
                'source_type' => 'payroll',
                'source_id' => null,

                'lines' => array_filter([

                    $totalBebanUmum > 0 ? [
                        'account' => '503.05.00',
                        'debit' => $totalBebanUmum
                    ] : null,

                    $totalBebanGaji > 0 ? [
                        'account' => '502.02.00',
                        'debit' => $totalBebanGaji
                    ] : null,

                    [
                        'account' => '101.00.02',
                        'credit' => $totalKasKeluar
                    ]

                ])
            ]);

            DB::commit();

            return redirect()
                ->route('payroll.index')
                ->with('status', 'edited');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($year, $month)
    {
        DB::beginTransaction();

        try {

            $periode = Carbon::create($year, $month, 1);

            // 🔥 Ambil payroll
            $payrolls = Payroll::whereYear('periode', $year)
                ->whereMonth('periode', $month)
                ->get();

            if ($payrolls->isEmpty()) {
                return redirect()
                    ->route('payroll.index')
                    ->with('error', 'Data payroll tidak ditemukan');
            }

            // 🔥 Ambil jurnal utama (bukan reversal)
            $journal = \App\Models\Journal::where('source_type', 'payroll')
                ->where('reference', 'PAYROLL-' . $month . '-' . $year)
                ->whereNull('reversal_of') // jurnal asli
                ->whereDoesntHave('reversedBy') // 🔥 belum direverse
                ->first();

            // 🔥 Reverse jurnal (kalau belum pernah direverse)
            if ($journal && !$journal->reversedBy) {
                AccountingHelper::reverse($journal, 'Hapus Payroll');
            }

            // 🔥 Hapus payroll
            Payroll::whereYear('periode', $year)
                ->whereMonth('periode', $month)
                ->delete();

            DB::commit();

            return redirect()
                ->route('payroll.index')
                ->with('status', 'Payroll berhasil dihapus & jurnal direverse');
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
