<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionController extends BaseController
{
    public function index(){
        $permissions = Permission::all();
        return view('pages.permissions.index', compact('permissions'));
    }

    public function generate(){
        $routes = Route::getRoutes()->getRoutes();

        foreach($routes as $route){            
            // Periksa apakah URI rute dimulai dengan 'JM'
            if (str_starts_with($route->uri(), 'JM')) {
                // inisialisasi route name
                $routeName = $route->getName();

                // cek jika nama route tidak ada dalam database maka input route ke dalam db
                if($routeName && !Permission::where('name', $routeName)->exists()){
                    Permission::create(['name'=> $routeName]);
                }
            }
        }

        return redirect()->back()->with('status', 'added');
    }
}
