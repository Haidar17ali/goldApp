<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaratController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StorageLocationController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/dashboard/stok/filter', [HomeController::class, 'filterStok'])->name('filter-stock');


Route::prefix('gold-app')
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

        // karat
        Route::get("/karat", [KaratController::class, 'index'])->name("karat.index");
        Route::get('/karat/buat', [KaratController::class, 'create'])->name('karat.buat');
        Route::post('/karat/buat', [KaratController::class, 'store'])->name('karat.simpan');
        Route::get('/karat/{id}/ubah', [KaratController::class, 'edit'])->name('karat.ubah');
        Route::patch('/karat/{id}/ubah', [KaratController::class, 'update'])->name('karat.update');
        Route::delete('/karat/{id}/hapus', [KaratController::class, 'destroy'])->name('karat.hapus');
        
        // storage location
        Route::get("/penyimpanan", [StorageLocationController::class, 'index'])->name("penyimpanan.index");
        Route::get('/penyimpanan/buat', [StorageLocationController::class, 'create'])->name('penyimpanan.buat');
        Route::post('/penyimpanan/buat', [StorageLocationController::class, 'store'])->name('penyimpanan.simpan');
        Route::get('/penyimpanan/{id}/ubah', [StorageLocationController::class, 'edit'])->name('penyimpanan.ubah');
        Route::patch('/penyimpanan/{id}/ubah', [StorageLocationController::class, 'update'])->name('penyimpanan.update');
        Route::delete('/penyimpanan/{id}/hapus', [StorageLocationController::class, 'destroy'])->name('penyimpanan.hapus');
        
        // branch
        Route::get("/cabang", [BranchController::class, 'index'])->name("cabang.index");
        Route::get('/cabang/buat', [BranchController::class, 'create'])->name('cabang.buat');
        Route::post('/cabang/buat', [BranchController::class, 'store'])->name('cabang.simpan');
        Route::get('/cabang/{branch}/ubah', [BranchController::class, 'edit'])->name('cabang.ubah');
        Route::patch('/cabang/{branch}/ubah', [BranchController::class, 'update'])->name('cabang.update');
        Route::delete('/cabang/{branch}/hapus', [BranchController::class, 'destroy'])->name('cabang.hapus');
        

        // product variant
        Route::get("/varian-produk", [ProductVariantController::class, 'index'])->name("varian-produk.index");
        Route::get('/varian-produk/buat', [ProductVariantController::class, 'create'])->name('varian-produk.buat');
        Route::post('/varian-produk/buat', [ProductVariantController::class, 'store'])->name('varian-produk.simpan');
        Route::get('/varian-produk/{id}/ubah', [ProductVariantController::class, 'edit'])->name('varian-produk.ubah');
        Route::patch('/varian-produk/{id}/ubah', [ProductVariantController::class, 'update'])->name('varian-produk.update');
        Route::get('/varian-produk/{id}/ubah/detail', [ProductVariantController::class, 'editDetail'])->name('varian-produk.ubahDetail');
        Route::patch('/varian-produk/{id}/ubah/detail', [ProductVariantController::class, 'updateDetail'])->name('varian-produk.updateDetail');
        Route::delete('/varian-produk/{id}/hapus', [ProductVariantController::class, 'destroy'])->name('varian-produk.hapus');
        Route::post('/varian-produk/import', [ProductVariantController::class, 'import'])->name('varian-produk.import');

        // product variant
        Route::get("/transaksi/{type}/{purchaseType}", [TransactionController::class, 'index'])->name("transaksi.index");
        Route::get('/transaksi/{type}/{purchaseType}/buat', [TransactionController::class, 'create'])->name('transaksi.buat');
        Route::post('/transaksi/{type}/{purchaseType}/buat', [TransactionController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/{type}/{purchaseType}/{id}/ubah', [TransactionController::class, 'edit'])->name('transaksi.ubah');
        Route::patch('/transaksi/{type}/{purchaseType}/{id}/ubah', [TransactionController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{id}/hapus', [TransactionController::class, 'destroy'])->name('transaksi.hapus');

        

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
