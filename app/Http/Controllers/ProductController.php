<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function index(){
        return view("pages.products.index");
    }

    public function create(){
        return view("pages.products.create");
    }

    public function store(Request $request){
        // validasai
        $request->validate([
            "code" => "unique:products|required",
            "name" => "required",
        ]);

        $data = [
            "code"=> strtolower($request->code),
            "name"=> strtolower($request->name),
        ];

        Product::create($data);
        return redirect()->route("produk.index")->with("status", "saved");
    }

    public function edit($id){
        $product = Product::findOrFail($id);
        return view("pages.products.edit", compact("product"));
    }

    public function update(Request $request, $id){
        $product = Product::findOrFail($id);
        // validasai
        $request->validate([
            "code" => "required|unique:products,code,".$id,
            "name" => "required",
        ]);

            $product->code = strtolower($request->code);
            $product->name = strtolower($request->name);
            $product->save();

        return redirect()->route("produk.index")->with("status", "edited");
    }

    public function destroy($id){
        Product::findOrFail($id)->delete();
        return redirect()->back()->with("status", "deleted");
    }
}
