<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(){
        return view("pages.colors.index");
    }

    public function create(){
        return view("pages.colors.create");
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

        Color::create($data);
        return redirect()->route("warna.index")->with("status", "saved");
    }

    public function edit($id){
        $product = Color::findOrFail($id);
        return view("pages.colors.edit", compact("product"));
    }

    public function update(Request $request, $id){
        $product = Color::findOrFail($id);
        // validasai
        $request->validate([
            "code" => "required|unique:products,code,".$id,
            "name" => "required",
        ]);

            $product->code = strtolower($request->code);
            $product->name = strtolower($request->name);
            $product->save();

        return redirect()->route("warna.index")->with("status", "edited");
    }

    public function destroy($id){
        Color::findOrFail($id)->delete();
        return redirect()->back()->with("status", "deleted");
    }
}
