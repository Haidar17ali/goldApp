<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(){
        $permissions = Permission::all();
        return view('pages.permissions.index', compact('permissions'));
    }

    public function generate(){
        // 1. Ambil semua route yang dimulai dengan 'by-zara'
        $routes = Route::getRoutes()->getRoutes();
        $routeNames = [];

        foreach ($routes as $route) {
            if (str_starts_with($route->uri(), 'gold-app')) {
                $routeName = $route->getName();

                if ($routeName) {
                    $routeNames[] = $routeName;

                    // Jika belum ada di DB → insert
                    if (!Permission::where('name', $routeName)->exists()) {
                        Permission::create(['name' => $routeName]);
                    }
                }
            }
        }

        // 2. Hapus permission lama yang tidak ada di routes
        Permission::where('name', 'like', 'gold-app%')
            ->whereNotIn('name', $routeNames)
            ->delete();

        // 3. Cek atau buat role super-admin
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);

        // 4. Assign semua permission ke super-admin
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);

        // 5. Assign role super-admin ke user pertama (id=1)
        $user = User::find(1);
        if ($user) {
            $user->assignRole($superAdmin);
        }

        return redirect()->back()->with('status', 'Permissions, Super Admin role, and User #1 synced');
    }


}
