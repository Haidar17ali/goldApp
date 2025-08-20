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
            $query->whereNull('used')
            ->orWhere("used", 0);
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
            ->where(function ($query) {
                $query->whereNull('used')
                    ->orWhere('used', 0);
            })
            ->orderByDesc('date')
            ->get()
            ->map(function ($lpb) {
                $totalKubikasi = 0;
                $stock130Afkir = 0;
                $stock130Super = 0;
                $stock260Super = 0;

                foreach ($lpb->details as $detail) {
                    $kubik = kubikasi($detail->diameter, $detail->length, $detail->qty);
                    $totalKubikasi += $kubik;

                    if ($detail->length == 130 && strtolower($detail->quality) == 'afkir') {
                        $stock130Afkir += $kubik;
                    } elseif ($detail->length == 130 && strtolower($detail->quality) == 'super') {
                        $stock130Super += $kubik;
                    } elseif ($detail->length == 260 && strtolower($detail->quality) == 'super') {
                        $stock260Super += $kubik;
                    }
                }

                return (object) [
                    'no_kitir' => $lpb->no_kitir,
                    'date' => $lpb->date,
                    'total_kubikasi' => $totalKubikasi,
                    'stock_130_afkir' => $stock130Afkir,
                    'stock_130_super' => $stock130Super,
                    'stock_260_super' => $stock260Super,
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

    public function filterStok(Request $request){
        $startDate = $request->input('start_date');
        $lastDate = $request->input('last_date') ?? $startDate;

        // LPB terpakai dalam rentang tanggal
        $lpbsTerpakai = LPB::where('used', true)
            ->whereBetween('used_at', [$startDate, $lastDate])
            ->with('details')
            ->get();

        $paidLpbs = LPB::where("paid_at", "!=", null)
            ->whereBetween('paid_at', [$startDate, $lastDate])
            ->with('details')
            ->get();

        $paidLpbData = [
            'reject_130' => 0,
            'super_130'  => 0,
            'super_260'  => 0,
        ];

        $stokTerpakai = [
            'reject_130' => 0,
            'super_130'  => 0,
            'super_260'  => 0,
        ];

        foreach ($paidLpbs as $index => $paidLpb) {
            foreach ($paidLpb->details as $detail) {
                if ($detail->quality == "Afkir" && $detail->length == 130) {
                    $paidLpbData["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                } elseif ($detail->quality == "Super") {
                    if ($detail->length == 130) {
                        $paidLpbData["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    } elseif ($detail->length == 260) {
                        $paidLpbData["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    }
                }
            }
        }

        foreach ($lpbsTerpakai as $index => $lpb) {
            foreach ($lpb->details as $detail) {
                if ($detail->quality == "Afkir" && $detail->length == 130) {
                    $stokTerpakai["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                } elseif ($detail->quality == "Super") {
                    if ($detail->length == 130) {
                        $stokTerpakai["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    } elseif ($detail->length == 260) {
                        $stokTerpakai["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    }
                }
            }
        }

        // LPB belum terpakai → belum digunakan sampai batas lastDate
        $stockGrouped = LpbDetail::whereHas('lpb', function ($query) use ($lastDate) {
            $query->where(function ($q) {
                $q->whereNull('used')
                ->orWhere('used', 0);
            })
            ->whereDate('date', '<=', $lastDate) // ⬅️ batas terakhir LPB
            ->orWhere('used_at', '>', $lastDate);
        })
        ->select('quality', 'length', 'diameter')
        ->selectRaw('SUM(qty) as total_qty')
        ->groupBy('quality', 'length', 'diameter')
        ->orderBy('quality')
        ->orderBy('length')
        ->orderBy('diameter')
        ->get();

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
        

        return response()->json([
            'stok_terpakai' => $stokTerpakai,
            'stok_belum_terpakai' => $stokBelumTerpakai,
            'paidLpbData' => $paidLpbData,
        ]);
    }

}
