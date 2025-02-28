<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LPBController;
use App\Http\Controllers\NPWPController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\POController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RoadPermitController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UtilityController;
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
        // import dan export karyawan
        Route::post('/import-karyawan', [EmployeeController::class, 'importEmployees'])->name('karyawan.import');
        // ajax dapatkan alamat yang ada di db
        Route::get('bagian/ajax', [EmployeeController::class, 'getAddress'])->name('karyawan.alamat');
        
        // supplier
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('/supplier/buat', [SupplierController::class, 'create'])->name('supplier.buat');
        Route::post('/supplier/buat', [SupplierController::class, 'store'])->name('supplier.simpan');
        Route::get('/supplier/{id}/ubah', [SupplierController::class, 'edit'])->name('supplier.ubah');
        Route::patch('/supplier/{id}/ubah', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{id}/hapus', [SupplierController::class, 'destroy'])->name('supplier.hapus');
        // import dan export supplier
        Route::post('/import-supplier', [SupplierController::class, 'importSupplier'])->name('supplier.import');

        // surat jalan
        Route::get('/surat-jalan/{type}', [RoadPermitController::class, 'index'])->name('surat-jalan.index');
        Route::get('/surat-jalan/{type}/buat', [RoadPermitController::class, 'create'])->name('surat-jalan.buat');
        Route::post('/surat-jalan/{type}/buat', [RoadPermitController::class, 'store'])->name('surat-jalan.simpan');
        Route::get('/surat-jalan/{id}/ubah/{type}', [RoadPermitController::class, 'edit'])->name('surat-jalan.ubah');
        Route::patch('/surat-jalan/{id}/ubah/{type}', [RoadPermitController::class, 'update'])->name('surat-jalan.update');
        Route::delete('/surat-jalan/{id}/hapus', [RoadPermitController::class, 'destroy'])->name('surat-jalan.hapus');
        Route::get('/surat-jalan/{id}/out', [RoadPermitController::class, 'out'])->name("surat-jalan.keluar");
        Route::get('/surat-jalan/{id}/set-handyman/{type}', [RoadPermitController::class, 'setHandyman'])->name('surat-jalan.set-pembongkar');
        Route::patch('/surat-jalan/{id}/set-handyman/{type}', [RoadPermitController::class, 'saveHandyman'])->name('surat-jalan.simpan-pembongkar');

        // npwp
        Route::get('/NPWP', [NPWPController::class, 'index'])->name('npwp.index');
        Route::get('/NPWP/buat', [NPWPController::class, 'create'])->name('npwp.buat');
        Route::post('/NPWP/buat', [NPWPController::class, 'store'])->name('npwp.simpan');
        Route::get('/NPWP/{id}/ubah', [NPWPController::class, 'edit'])->name('npwp.ubah');
        Route::patch('/NPWP/{id}/ubah', [NPWPController::class, 'update'])->name('npwp.update');
        Route::delete('/NPWP/{id}/hapus', [NPWPController::class, 'destroy'])->name('npwp.hapus');
        // import dan export NPWP
        Route::post('/import-NPWP', [NPWPController::class, 'importNPWP'])->name('npwp.import');
        
        // Log
        Route::get('/log/{type}', [LogController::class, 'index'])->name('log.index');
        Route::get('/log/{type}/buat', [LogController::class, 'create'])->name('log.buat');
        Route::post('/log/{type}/buat', [LogController::class, 'store'])->name('log.simpan');
        Route::get('/log/{id}/ubah/{type}', [LogController::class, 'edit'])->name('log.ubah');
        Route::patch('/log/{id}/ubah/{type}', [LogController::class, 'update'])->name('log.update');
        Route::delete('/log/{id}/hapus', [LogController::class, 'destroy'])->name('log.hapus');
        // import dan export log
        Route::post('/import-log/{type}', [LogController::class, 'importLog'])->name('log.import');

        // po
        Route::get('/purchase-order/{type}', [POController::class, 'index'])->name('purchase-order.index');
        Route::get('/purchase-order/{type}/buat', [POController::class, 'create'])->name('purchase-order.buat');
        Route::post('/purchase-order/{type}/buat', [POController::class, 'store'])->name('purchase-order.simpan');
        Route::get('/purchase-order/{id}/ubah/{type}', [POController::class, 'edit'])->name('purchase-order.ubah');
        Route::patch('/purchase-order/{id}/ubah/{type}', [POController::class, 'update'])->name('purchase-order.update');
        Route::delete('/purchase-order/{id}/hapus', [POController::class, 'destroy'])->name('purchase-order.hapus');

        // lpb
        Route::get('/lpb', [LPBController::class, 'index'])->name('lpb.index');
        Route::get('/lpb/buat', [LPBController::class, 'create'])->name('lpb.buat');
        Route::post('/lpb/buat', [LPBController::class, 'store'])->name('lpb.simpan');
        Route::get('/lpb/{id}/ubah/', [LPBController::class, 'edit'])->name('lpb.ubah');
        Route::patch('/lpb/{id}/ubah/', [LPBController::class, 'update'])->name('lpb.update');
        Route::delete('/lpb/{id}/hapus', [LPBController::class, 'destroy'])->name('lpb.hapus');

        // utility
            // ajax
        Route::get('no-rek-id/ajax', [UtilityController::class, 'getNumberAccount'])->name('utility.ajax-no-rek');
        Route::get('npwp-id/ajax', [UtilityController::class, 'getByID'])->name('utility.npwpId');
        Route::get('surat-jalan-id/ajax', [UtilityController::class, 'getByID'])->name('utility.suratJalanId');
            // po
        Route::get('/persetujuan-po/{modelType}/{id}/{status}', [UtilityController::class, 'approve'])->name('utility.approve-po');
        Route::get('/aktivasi-po/{modelType}/{id}/{status}', [UtilityController::class, 'activation'])->name('utility.activation-po');
            // LPB
        Route::get('/persetujuan-LPB/{modelType}/{id}/{status}', [UtilityController::class, 'approve'])->name('utility.approve-lpb');
    });
