<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LPB;
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
        // $routes = Route::getRoutes()->getRoutes();

        // $adminRoutes = [];
        // foreach($routes as $route){
        //     // Periksa apakah URI rute dimulai dengan 'admin'
        //     if (str_starts_with($route->uri(), 'JM')) {
        //        $adminRoutes[] = $route->getName();
        //     }
        // }
        // dd($adminRoutes);

        $users = User::all();
        $employees = Employee::all();
        $roadPermits = RoadPermit::all();
        $lpbs = LPB::with(['details', "npwp"])->whereMonth('date', now())->get();

        $topNpwpData = \App\Models\NPWP::with(['lpbs.details'])
        ->get()
        ->map(function ($npwp) {
            $totalUang = 0;
            foreach ($npwp->lpbs as $lpb) {
                foreach ($lpb->details as $detail) {
                    $totalUang += kubikasi($detail->diameter,$detail->length, $detail->qty) * $detail->price;
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

        // chart stock sengon
        $pi = 3.1416;

        // Ambil semua LPB yang belum terpakai
        $lpbsBelumTerpakai = LPB::where('used', null)->with('details')->get();

        // Hitung stok belum terpakai berdasarkan kualitas
        $stokBelumTerpakai = [
            'reject_130' => 0,
            'super_130'  => 0,
            'super_260'  => 0,
        ];

        $totalKubikasi = 0;

        foreach ($lpbsBelumTerpakai as $lpb) {
            foreach ($lpb->details as $detail) {
                $quality = strtolower(str_replace(' ', '_', $detail->quality)); // contoh: 'Reject 130' → 'reject_130'
                
                if($quality == "afkir" && $detail->length == 130){
                    $stokBelumTerpakai["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                }elseif($quality == "super"){
                    if($detail->length == 130){
                        $stokBelumTerpakai["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    }elseif($detail->length == 260){
                        $stokBelumTerpakai["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    }
                }
            }
        }

        // Terpakai hari ini
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
                $quality = strtolower(str_replace(' ', '_', $detail->quality)); // contoh: 'Reject 130' → 'reject_130'
                
                if($quality == "afkir" && $detail->length == 130){
                    $stokTerpakaiHariIni["reject_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                }elseif($quality == "super"){
                    if($detail->length == 130){
                        $stokTerpakaiHariIni["super_130"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    }elseif($detail->length == 260){
                        $stokTerpakaiHariIni["super_260"] += kubikasi($detail->diameter, $detail->length, $detail->qty);
                    }
                }
            }
        }

        return view('home', compact(['users', 'employees', 'roadPermits', "lpbs", "topNpwpData", 'stokBelumTerpakai', "stokTerpakaiHariIni"]));
    }
}
