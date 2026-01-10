<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaratController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerSupplierController;
use App\Http\Controllers\GoldConversionController;
use App\Http\Controllers\GoldMergeConversionController;
use App\Http\Controllers\GoldManagementController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StorageLocationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/dashboard/stok/filter', [HomeController::class, 'filterStok'])->name('filter-stock');


Route::prefix('gold-app')
    ->middleware(['auth'])
    ->group(function () {

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

        // rekening
        Route::get("/rekening", [BankAccountController::class, 'index'])->name("rekening.index");
        Route::get('/rekening/buat', [BankAccountController::class, 'create'])->name('rekening.buat');
        Route::post('/rekening/buat', [BankAccountController::class, 'store'])->name('rekening.simpan');
        Route::get('/rekening/{bankAccount}/ubah', [BankAccountController::class, 'edit'])->name('rekening.ubah');
        Route::patch('/rekening/{bankAccount}/ubah', [BankAccountController::class, 'update'])->name('rekening.update');
        Route::delete('/rekening/{bankAccount}/hapus', [BankAccountController::class, 'destroy'])->name('rekening.hapus');

        // customer & supplier
        Route::get("/customer-supplier/{type}", [CustomerSupplierController::class, 'index'])->name("customer-supplier.index");
        Route::get('/customer-supplier/{type}/buat', [CustomerSupplierController::class, 'create'])->name('customer-supplier.buat');
        Route::post('/customer-supplier/{type}/buat', [CustomerSupplierController::class, 'store'])->name('customer-supplier.simpan');
        Route::get('/customer-supplier/{type}/{id}/ubah', [CustomerSupplierController::class, 'edit'])->name('customer-supplier.ubah');
        Route::patch('/customer-supplier/{type}/{id}/ubah', [CustomerSupplierController::class, 'update'])->name('customer-supplier.update');
        Route::delete('/customer-supplier/{type}/{id}/hapus', [CustomerSupplierController::class, 'destroy'])->name('customer-supplier.hapus');


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
        Route::get('/varian-produk/barcode/{id}', [ProductVariantController::class, 'barcodeForm'])->name('varian-produk.barcode-form');
        Route::post('/barcode/{id}/print', [ProductVariantController::class, 'barcodePrint'])->name('varian-produk.barcode-print');    

        // transaksi pembelian
        Route::get("/transaksi/{type}/{purchaseType}", [TransactionController::class, 'index'])->name("transaksi.index");
        Route::get('/transaksi/{type}/{purchaseType}/buat', [TransactionController::class, 'create'])->name('transaksi.buat');
        Route::post('/transaksi/{type}/{purchaseType}/buat', [TransactionController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/{type}/{purchaseType}/{transaction}/ubah', [TransactionController::class, 'edit'])->name('transaksi.ubah');
        Route::patch('/transaksi/{type}/{purchaseType}/{id}/update', [TransactionController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi{type}/{purchaseType}/{transaction}/hapus', [TransactionController::class, 'destroy'])->name('transaksi.hapus');

        // Penjualan
        Route::get("/transaction/{type}/emas", [SalesController::class, 'index'])->name("penjualan.index");
        Route::get('/transaction/{type}/emas/buat', [SalesController::class, 'create'])->name('penjualan.buat');
        Route::post('/transaction/{type}/emas/buat', [SalesController::class, 'store'])->name('penjualan.simpan');
        Route::get('/transaction/{type}/emas/{id}/ubah', [SalesController::class, 'edit'])->name('penjualan.ubah');
        Route::patch('/transaction/{type}/emas/{id}/ubah', [SalesController::class, 'update'])->name('penjualan.update');
        Route::delete('/transaction/{type}/emas/{id}/hapus', [SalesController::class, 'destroy'])->name('penjualan.hapus');
        // cetak penjualan
        Route::get('penjualan/cetak/{id}', [SalesController::class, "print"])->name("penjualan.cetak");

        // opname
        Route::get("/opname", [StockAdjustmentController::class, 'index'])->name("opname.index");
        Route::get('/opname/buat', [StockAdjustmentController::class, 'create'])->name('opname.buat');
        Route::post('/opname/buat', [StockAdjustmentController::class, 'store'])->name('opname.simpan');
        Route::get('/opname/import', [StockAdjustmentController::class, 'importForm'])->name('opname.import-form');
        Route::post('/opname/import', [StockAdjustmentController::class, 'import'])->name('opname.import');
        Route::delete('/opname/{bankAccount}/hapus', [StockAdjustmentController::class, 'destroy'])->name('opname.hapus');
        Route::get('/opname/get-stock', [StockAdjustmentController::class, 'getStock'])->name('opname.dapatStock');

        // pengelolaan emas
        Route::get("/pengelolaan-emas", [GoldManagementController::class, 'index'])->name("pengelolaan-emas.index");
        Route::get('/pengelolaan-emas/buat', [GoldManagementController::class, 'create'])->name('pengelolaan-emas.buat');
        Route::post('/pengelolaan-emas/buat', [GoldManagementController::class, 'store'])->name('pengelolaan-emas.simpan');
        Route::get('/pengelolaan-emas/{id}/ubah', [GoldManagementController::class, 'edit'])->name('pengelolaan-emas.ubah');
        Route::patch('/pengelolaan-emas/{id}/ubah', [GoldManagementController::class, 'update'])->name('pengelolaan-emas.update');
        Route::delete('/pengelolaan-emas/{id}/hapus', [GoldManagementController::class, 'destroy'])->name('pengelolaan-emas.hapus');

        // conversi emas etalase
        Route::get("/konversi-emas", [GoldConversionController::class, 'index'])->name("konversi-emas.index");
        Route::get('/konversi-emas/buat', [GoldConversionController::class, 'create'])->name('konversi-emas.buat');
        Route::post('/konversi-emas/buat', [GoldConversionController::class, 'store'])->name('konversi-emas.simpan');
        Route::get('/konversi-emas/{id}/ubah', [GoldConversionController::class, 'edit'])->name('konversi-emas.ubah');
        Route::get('/konversi-emas/{id}/detail', [GoldConversionController::class, 'show'])->name('konversi-emas.detail');
        Route::patch('/konversi-emas/{id}/ubah', [GoldConversionController::class, 'update'])->name('konversi-emas.update');
        Route::delete('/konversi-emas/{id}/hapus', [GoldConversionController::class, 'destroy'])->name('konversi-emas.hapus');

        // conversi emas brankas
        Route::get("/keluar-etalase", [GoldMergeConversionController::class, 'index'])->name("keluar-etalase.index");
        Route::get('/keluar-etalase/buat', [GoldMergeConversionController::class, 'create'])->name('keluar-etalase.buat');
        Route::post('/keluar-etalase/buat', [GoldMergeConversionController::class, 'store'])->name('keluar-etalase.simpan');
        Route::get('/keluar-etalase/{id}/ubah', [GoldMergeConversionController::class, 'edit'])->name('keluar-etalase.ubah');
        Route::get('/keluar-etalase/{id}/detail', [GoldMergeConversionController::class, 'show'])->name('keluar-etalase.detail');
        Route::patch('/keluar-etalase/{id}/ubah', [GoldMergeConversionController::class, 'update'])->name('keluar-etalase.update');
        Route::delete('/keluar-etalase/{id}/hapus', [GoldMergeConversionController::class, 'destroy'])->name('keluar-etalase.hapus');


        // 🔹 Tambahkan ini untuk AJAX info stok per karat
        Route::get('/stock/info/{karat}', function ($karatId) {
            $stock = \App\Models\Stock::where('karat_id', $karatId)
                ->where('type', 'customer')
                ->selectRaw('SUM(weight) as weight')
                ->first();

            return response()->json(['weight' => $stock->weight ?? 0]);
        });

        // stocks
        Route::get('/stocks', [StockController::class, 'index'])->name("stock.index");
        // routes/web.php
        Route::get('/stocks/detail', [StockController::class, 'detail'])->name('stocks.detail');
        Route::get('/stocks/info', [StockController::class, 'info'])->name("stock.info");
        Route::get('/stocks/weights', [StockController::class, 'weights'])->name("stock.berat");




        // utility
        // ajax
        Route::get('no-rek-id/ajax', [UtilityController::class, 'getNumberAccount'])->name('utility.ajax-no-rek');
        Route::get('npwp-id/ajax', [UtilityController::class, 'getByID'])->name('utility.getById');
        Route::get('npwp-id/ajax/multiple-data', [UtilityController::class, 'getMultipleData'])->name('utility.getMultipleData');
        Route::get('surat-jalan-id/ajax', [UtilityController::class, 'getByID'])->name('utility.suratJalanId');


        // search data
        Route::get('data/search', [UtilityController::class, 'search'])->name('search');

        // print data
        Route::get('cetak/surat-jalan', [UtilityController::class, 'getByID'])->name('utility.cetak-surat-jalan');


        Route::post('/backup/export', [BackupController::class, 'export'])->name('backup.export');
    });
