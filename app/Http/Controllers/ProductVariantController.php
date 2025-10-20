<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Karat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
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


    public function store(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'karat_id'   => 'nullable|exists:karats,id',
            'gram'    => 'required',
            'default_price' => 'nullable|numeric|min:0',
        ]);

        $product   = Product::find($request->product_id);
        $karatName = $request->karat_id ? Karat::find($request->karat_id)->name : 'NOKRT';

        // SKU format: PRODUCTCODE-karatCODE-SIZECODE
        $sku = strtoupper($product->name . '-' . $karatName. '-' . $request->gram);

        // Barcode: unik (pakai UUID atau kombinasi angka acak)
        $barcode = strtoupper(Str::random(12));

        $variant = ProductVariant::create([
            'product_id' => $request->product_id,
            'karat_id'   => $request->karat_id,
            'gram'    => $request->gram,
            'sku'        => $sku,
            'barcode'    => $barcode,
            'default_price' => $request->default_price,
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

    public function update(Request $request, $id){
        $productVariant = ProductVariant::findOrFail($id);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'karat_id'   => 'nullable|exists:karats,id',
            'gram'    => 'required',
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
}
