<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
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
        $colors   = Color::all();
        $sizes    = Size::all();
        return view('pages.product-variants.create', compact('products', 'colors', 'sizes'));
    }


    public function store(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id'   => 'nullable|exists:colors,id',
            'size_id'    => 'nullable|exists:sizes,id',
            'default_price' => 'nullable|numeric|min:0',
        ]);

        $product   = Product::find($request->product_id);
        $colorName = $request->color_id ? Color::find($request->color_id)->name : 'NOCLR';
        $sizeCode  = $request->size_id ? Size::find($request->size_id)->code : 'NOSIZE';

        // SKU format: PRODUCTCODE-COLORCODE-SIZECODE
        $sku = strtoupper($product->name . '-' . $colorName . '-' . $sizeCode);

        // Barcode: unik (pakai UUID atau kombinasi angka acak)
        $barcode = strtoupper(Str::random(12));

        $variant = ProductVariant::create([
            'product_id' => $request->product_id,
            'color_id'   => $request->color_id,
            'size_id'    => $request->size_id,
            'sku'        => $sku,
            'barcode'    => $barcode,
            'default_price' => $request->default_price,
        ]);

        return redirect()->route('varian-produk.index')
            ->with('success', 'Product variant berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $productVariant = ProductVariant::findOrFail($id);
        $products = Product::all();
        $colors   = Color::all();
        $sizes    = Size::all();
        return view('pages.product-variants.edit', compact('productVariant', 'products', 'colors', 'sizes'));
    }

    public function update(Request $request, $id){
        $productVariant = ProductVariant::findOrFail($id);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id'   => 'nullable|exists:colors,id',
            'size_id'    => 'nullable|exists:sizes,id',
            'default_price' => 'nullable|numeric|min:0',
        ]);

        $product   = Product::find($request->product_id);
        $colorName = $request->color_id ? Color::find($request->color_id)->name : 'NOCLR';
        $sizeCode  = $request->size_id ? Size::find($request->size_id)->code : 'NOSIZE';

        // SKU regenerate setiap kali edit product/color/size
        $sku = strtoupper($product->name . '-' . $colorName . '-' . $sizeCode);

        $productVariant->update([
            'product_id' => $request->product_id,
            'color_id'   => $request->color_id,
            'size_id'    => $request->size_id,
            'sku'        => $sku,
            // barcode tidak berubah biar tetap unik (kalau mau regenerate bisa juga)
            'default_price' => $request->default_price,
        ]);

        return redirect()->route('varian-produk.index')
            ->with('success', 'Product variant berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $productVariant = ProductVariant::findOrFail($id);
        $productVariant->delete();
        return redirect()->route('varian-produk.index')->with('status', 'deleted');
    }
}
