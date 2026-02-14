<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Karat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductVariantImport;

class ProductVariantController extends BaseController
{
    public function index()
    {
        return view('pages.product-variants.index');
    }

    public function create()
    {
        $products = Product::all();
        $karats   = Karat::all();
        return view('pages.product-variants.create', compact('products', 'karats'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'karat_id'       => 'nullable|exists:karats,id',
            'gram'           => 'nullable|numeric|min:0.01',
            'type'           => 'required|in:new,sepuh',
            'default_price'  => 'nullable|numeric|min:0',
        ]);


        $product   = Product::find($request->product_id);
        $karatName = $request->karat_id
            ? Karat::find($request->karat_id)->name
            : 'NOKRT';

        $type = strtoupper($request->type); // NEW / SEPUH

        $sku = strtoupper(
            $product->name . '-' .
                $karatName . '-' .
                $request->gram . '-' .
                $type
        );


        // Barcode: unik (pakai UUID atau kombinasi angka acak)
        $barcode = strtoupper(Str::random(6));

        ProductVariant::create([
            'product_id'     => $request->product_id,
            'karat_id'       => $request->karat_id,
            'gram'           => $request->gram,
            'type'           => $request->type, // 🔥 PENTING
            'sku'            => $sku,
            'barcode'        => $barcode,
            'default_price'  => $request->default_price,
        ]);

        return redirect()->route('varian-produk.index')
            ->with('status', 'saved');
    }


    public function edit($id)
    {
        $productVariant = ProductVariant::findOrFail($id);
        $products = Product::all();
        $karats   = Karat::all();
        return view('pages.product-variants.edit', compact('productVariant', 'products', 'karats'));
    }

    public function update(Request $request, $id)
    {
        $productVariant = ProductVariant::findOrFail($id);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'karat_id'   => 'nullable|exists:karats,id',
            'gram'    => 'nullable',
            'default_price' => 'nullable|numeric|min:0',
        ]);

        $product   = Product::find($request->product_id);
        $karatName = $request->karat_id ? Karat::find($request->karat_id)->name : 'NOKRT';

        // SKU regenerate setiap kali edit product/karat/size
        $sku = strtoupper($product->name . '-' . $karatName . '-' . $request->gram);

        $productVariant->update([
            'product_id' => $request->product_id,
            'karat_id'   => $request->karat_id,
            'gram'    => $request->gram,
            'sku'        => $sku,
            'type'    => $request->type,
            // barcode tidak berubah biar tetap unik (kalau mau regenerate bisa juga)
            'default_price' => $request->default_price,
        ]);

        return redirect()->route('varian-produk.index')
            ->with('status', 'saved');
    }


    public function destroy($id)
    {
        $productVariant = ProductVariant::findOrFail($id);
        $productVariant->delete();
        return redirect()->route('varian-produk.index')->with('status', 'deleted');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        Excel::import(new ProductVariantImport, $request->file('file'));

        return redirect()->route('varian-produk.index')->with('status', 'imported');
    }

    public function barcodeForm($id)
    {
        $item = ProductVariant::with(['product', 'karat'])->findOrFail($id);

        return view('pages.product-variants.barcode-form', compact('item'));
    }

    public function barcodePrint(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:100'
        ]);

        $item = ProductVariant::with(['product', 'karat'])->findOrFail($id);

        return view('pages.product-variants.barcode-print', [
            'item' => $item,
            'qty'  => $request->qty
        ]);
    }
}
