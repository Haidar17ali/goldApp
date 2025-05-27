<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LPB;
use App\Models\LPBDetail;
use App\Models\npwp;
use App\Models\RoadPermit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    public function index()
{
    $users = User::all();
    $employees = Employee::all();
    $roadPermits = RoadPermit::all();

    // Data stok belum dipakai, dikelompokkan
    $stockGrouped = LpbDetail::whereHas('lpb', function ($query) {
        $query->whereNull('used');
    })
    ->select('quality', 'length', 'diameter')
    ->selectRaw('SUM(qty) as total_qty')
    ->groupBy('quality', 'length', 'diameter')
    ->orderBy('quality')
    ->orderBy('length')
    ->orderBy('diameter')
    ->get();

    // LPB bulan ini
    $lpbs = LPB::with(['details', 'npwp'])
        ->whereMonth('date', now())
        ->get();

    // LPB belum dipakai → hanya ambil tgl kirim, no_kitir, dan total kubikasi
    $lpbsBelumTerpakai = LPB::with('details')
    ->where('used', null)
    ->orderByDesc('date')
    ->get()
    ->map(function ($lpb) {
        $totalKubikasi = 0;
        foreach ($lpb->details as $detail) {
            $totalKubikasi += kubikasi($detail->diameter, $detail->length, $detail->qty);
        }

        return (object) [
            'no_kitir' => $lpb->no_kitir,
            'date' => $lpb->date,
            'total_kubikasi' => $totalKubikasi,
        ];
    });

    $totalKubikasiLpbBelumTerpakai = $lpbsBelumTerpakai->sum('total_kubikasi');

    // Data Top NPWP
    $topNpwpData = \App\Models\NPWP::with(['lpbs.details'])
        ->get()
        ->map(function ($npwp) {
            $totalUang = 0;
            foreach ($npwp->lpbs as $lpb) {
                foreach ($lpb->details as $detail) {
                    $totalUang += kubikasi($detail->diameter, $detail->length, $detail->qty) * $detail->price;
                }
            }

            return (object) [
                'nama_npwp' => $npwp->name,
                'total_uang' => $totalUang,
            ];
        })
        ->sortByDesc('total_uang')
        ->take(7)
        ->values();

    // Hitung stok belum terpakai berdasarkan kualitas
    $stokBelumTerpakai = [
        'reject_130' => 0,
        'super_130'  => 0,
        'super_260'  => 0,
    ];

    foreach ($stockGrouped as $detail) {
        if ($detail->quality == "Afkir" && $detail->length == 130) {
            $stokBelumTerpakai["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->total_qty);
        } elseif ($detail->quality == "Super") {
            if ($detail->length == 130) {
                $stokBelumTerpakai["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->total_qty);
            } elseif ($detail->length == 260) {
                $stokBelumTerpakai["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->total_qty);
            }
        }
    }

    // Stok terpakai hari ini
    $lpbsTerpakaiHariIni = LPB::where('used', true)
        ->whereDate('used_at', today())
        ->with('details')
        ->get();

    $stokTerpakaiHariIni = [
        'reject_130' => 0,
        'super_130'  => 0,
        'super_260'  => 0,
    ];

    foreach ($lpbsTerpakaiHariIni as $lpb) {
        foreach ($lpb->details as $detail) {
            $quality = strtolower(str_replace(' ', '_', $detail->quality));

            if ($quality == "afkir" && $detail->length == 130) {
                $stokTerpakaiHariIni["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
            } elseif ($quality == "super") {
                if ($detail->length == 130) {
                    $stokTerpakaiHariIni["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                } elseif ($detail->length == 260) {
                    $stokTerpakaiHariIni["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                }
            }
        }
    }

    return view('home', compact([
        'users',
        'employees',
        'roadPermits',
        'lpbs',
        'lpbsBelumTerpakai',
        'topNpwpData',
        'stokBelumTerpakai',
        'stokTerpakaiHariIni',
        'totalKubikasiLpbBelumTerpakai'
    ]));
}

}
