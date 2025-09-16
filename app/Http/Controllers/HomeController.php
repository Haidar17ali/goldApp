<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    public function index()
    {
        $users = User::all();

        return view('home', compact([
            'users',
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
