<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::prefix('JM')
    ->middleware(['auth'])
    ->group(function(){

        // permissions
        Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
        Route::post('/permission/generate', [PermissionController::class, 'generate'])->name('permission.generate');

        // roles
        Route::get("/role", [RoleController::class, "index"])->name('role.index');
        Route::get("/role/buat", [RoleController::class, "create"])->name('role.buat');
        Route::post("/role/buat", [RoleController::class, "store"])->name('role.simpan');
        Route::get("/role/{id}/ubah", [RoleController::class, "edit"])->name('role.ubah');
        Route::patch("/role/{id}/ubah", [RoleController::class, "update"])->name('role.update');
        Route::delete("/role/{id}/hapus", [RoleController::class, "delete"])->name('role.hapus');

    });
