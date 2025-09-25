<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CuttingController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/dashboard/stok/filter', [HomeController::class, 'filterStok'])->name('filter-stock');


Route::prefix('by-zara')
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

        // users
        Route::get("/pengguna", [UserController::class, 'index'])->name("pengguna.index");
        Route::get('/pengguna/buat', [UserController::class, 'create'])->name('pengguna.buat');
        Route::post('/pengguna/buat', [UserController::class, 'store'])->name('pengguna.simpan');
        Route::get('/pengguna/{id}/ubah', [UserController::class, 'edit'])->name('pengguna.ubah');
        Route::patch('/pengguna/{id}/ubah', [UserController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{id}/hapus', [UserController::class, 'destroy'])->name('pengguna.hapus');

        // products
        Route::get("/produk", [ProductController::class, 'index'])->name("produk.index");
        Route::get('/produk/buat', [ProductController::class, 'create'])->name('produk.buat');
        Route::post('/produk/buat', [ProductController::class, 'store'])->name('produk.simpan');
        Route::get('/produk/{id}/ubah', [ProductController::class, 'edit'])->name('produk.ubah');
        Route::patch('/produk/{id}/ubah', [ProductController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}/hapus', [ProductController::class, 'destroy'])->name('produk.hapus');

        // colors
        Route::get("/warna", [ColorController::class, 'index'])->name("warna.index");
        Route::get('/warna/buat', [ColorController::class, 'create'])->name('warna.buat');
        Route::post('/warna/buat', [ColorController::class, 'store'])->name('warna.simpan');
        Route::get('/warna/{id}/ubah', [ColorController::class, 'edit'])->name('warna.ubah');
        Route::patch('/warna/{id}/ubah', [ColorController::class, 'update'])->name('warna.update');
        Route::delete('/warna/{id}/hapus', [ColorController::class, 'destroy'])->name('warna.hapus');

        // sizes
        Route::get("/size", [SizeController::class, 'index'])->name("size.index");
        Route::get('/size/buat', [SizeController::class, 'create'])->name('size.buat');
        Route::post('/size/buat', [SizeController::class, 'store'])->name('size.simpan');
        Route::get('/size/{id}/ubah', [SizeController::class, 'edit'])->name('size.ubah');
        Route::patch('/size/{id}/ubah', [SizeController::class, 'update'])->name('size.update');
        Route::delete('/size/{id}/hapus', [SizeController::class, 'destroy'])->name('size.hapus');

        // cutting
        Route::get("/cutting", [CuttingController::class, 'index'])->name("cutting.index");
        Route::get('/cutting/buat', [CuttingController::class, 'create'])->name('cutting.buat');
        Route::post('/cutting/buat', [CuttingController::class, 'store'])->name('cutting.simpan');
        Route::get('/cutting/{id}/ubah', [CuttingController::class, 'edit'])->name('cutting.ubah');
        Route::patch('/cutting/{id}/ubah', [CuttingController::class, 'update'])->name('cutting.update');
        Route::get('/cutting/{id}/ubah/detail', [CuttingController::class, 'editDetail'])->name('cutting.ubahDetail');
        Route::patch('/cutting/{id}/ubah/detail', [CuttingController::class, 'updateDetail'])->name('cutting.updateDetail');
        Route::delete('/cutting/{id}/hapus', [CuttingController::class, 'destroy'])->name('cutting.hapus');
        Route::post('/cutting/update-Status', [CuttingController::class, 'updateStatus'])->name('cutting.updateStatus');
        
        // deliveries
        Route::get("/pengiriman", [DeliveryController::class, 'index'])->name("pengiriman.index");
        Route::get('/pengiriman/buat', [DeliveryController::class, 'create'])->name('pengiriman.buat');
        Route::post('/pengiriman/buat', [DeliveryController::class, 'store'])->name('pengiriman.simpan');
        Route::get('/pengiriman/{id}/ubah', [DeliveryController::class, 'edit'])->name('pengiriman.ubah');
        Route::patch('/pengiriman/{id}/ubah', [DeliveryController::class, 'update'])->name('pengiriman.update');
        Route::get('/pengiriman/{id}/ubah/detail', [DeliveryController::class, 'editDetail'])->name('pengiriman.ubahDetail');
        Route::patch('/pengiriman/{id}/ubah/detail', [DeliveryController::class, 'updateDetail'])->name('pengiriman.updateDetail');
        Route::delete('/pengiriman/{id}/hapus', [DeliveryController::class, 'destroy'])->name('pengiriman.hapus');

        // product variant
        Route::get("/varian-produk", [ProductVariantController::class, 'index'])->name("varian-produk.index");
        Route::get('/varian-produk/buat', [ProductVariantController::class, 'create'])->name('varian-produk.buat');
        Route::post('/varian-produk/buat', [ProductVariantController::class, 'store'])->name('varian-produk.simpan');
        Route::get('/varian-produk/{id}/ubah', [ProductVariantController::class, 'edit'])->name('varian-produk.ubah');
        Route::patch('/varian-produk/{id}/ubah', [ProductVariantController::class, 'update'])->name('varian-produk.update');
        Route::get('/varian-produk/{id}/ubah/detail', [ProductVariantController::class, 'editDetail'])->name('varian-produk.ubahDetail');
        Route::patch('/varian-produk/{id}/ubah/detail', [ProductVariantController::class, 'updateDetail'])->name('varian-produk.updateDetail');
        Route::delete('/varian-produk/{id}/hapus', [ProductVariantController::class, 'destroy'])->name('varian-produk.hapus');

        

        // utility
            // ajax
        Route::get('no-rek-id/ajax', [UtilityController::class, 'getNumberAccount'])->name('utility.ajax-no-rek');
        Route::get('npwp-id/ajax', [UtilityController::class, 'getByID'])->name('utility.getById');
        Route::get('npwp-id/ajax/multiple-data', [UtilityController::class, 'getMultipleData'])->name('utility.getMultipleData');
        Route::get('surat-jalan-id/ajax', [UtilityController::class, 'getByID'])->name('utility.suratJalanId');
            // po
        Route::get('/persetujuan-po/{modelType}/{id}/{status}', [UtilityController::class, 'approve'])->name('utility.approve-po');
        Route::get('/aktivasi-po/{modelType}/{id}/{status}', [UtilityController::class, 'activation'])->name('utility.activation-po');

            // dp
        Route::get('/aktivasi-dp/{modelType}/{id}/{status}', [UtilityController::class, 'activation'])->name('utility.activation-dp');
        Route::get('/dp-menunggu-pembayaran/', [UtilityController::class, 'getByType'])->name('utility.dp-type'); //get dp for payment

            // LPB
        Route::get('/persetujuan-LPB/{modelType}/{id}/{status}', [UtilityController::class, 'approve'])->name('utility.approve-lpb');
        Route::get('/get-lpb-detail/detail', [UtilityController::class, 'getById'])->name('utility.lpb-ajax-detail');


        // search data
        Route::get('data/search',[UtilityController::class, 'search'])->name('search');

        // print data
        Route::get('cetak/surat-jalan', [UtilityController::class, 'getByID'])->name('utility.cetak-surat-jalan');


        Route::post('/backup/export', [BackupController::class, 'export'])->name('backup.export');
    });
