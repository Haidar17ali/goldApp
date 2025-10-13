<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $routes = Route::getRoutes()->getRoutes();

        foreach($routes as $route){
            
            // Periksa apakah URI rute dimulai dengan 'JM'
            if (str_starts_with($route->uri(), 'gold-app')) {
                // inisialisasio route name
                $routeName = $route->getName();
                if($routeName && !Permission::where('name', $routeName)->exists()){
                    Permission::create(['name'=> $routeName]);
                }
            }
        }
    }
}
