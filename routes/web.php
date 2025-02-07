<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
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
        Route::delete("/role/{id}/hapus", [RoleController::class, "destroy"])->name('role.hapus');

        // position
        Route::get('/bagian', [PositionController::class, 'index'])->name('bagian.index');
        Route::get("/bagian/buat", [PositionController::class, "create"])->name('bagian.buat');
        Route::post("/bagian/buat", [PositionController::class, "store"])->name('bagian.simpan');
        Route::get("/bagian/{id}/ubah", [PositionController::class, "edit"])->name('bagian.ubah');
        Route::patch("/bagian/{id}/ubah", [PositionController::class, "update"])->name('bagian.update');
        Route::delete("/bagian/{id}/hapus", [PositionController::class, "destroy"])->name('bagian.hapus');
        // ajax position
        Route::get('bagian/ajax', [PositionController::class, 'selectType'])->name('bagian.tipe');

        // Karyawan
        Route::get('/karyawan', [EmployeeController::class, 'index'])->name('karyawan.index');
        Route::get('/karyawan/buat', [EmployeeController::class, 'create'])->name('karyawan.buat');
        Route::post('/karyawan/buat', [EmployeeController::class, 'store'])->name('karyawan.simpan');
        Route::get('/karyawan/{id}/ubah', [EmployeeController::class, 'edit'])->name('karyawan.ubah');
        Route::patch('/karyawan/{id}/ubah', [EmployeeController::class, 'update'])->name('karyawan.update');
        Route::delete('/karyawan/{id}/hapus', [EmployeeController::class, 'destroy'])->name('karyawan.hapus');
    });
