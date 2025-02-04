<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
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

        return view('home');
    }
}
