<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index(){
        return view("pages.types.index");
    }

    public function create(){
        return view("pages.types.create");
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

        Type::create($data);
        return redirect()->route("jenis.index")->with("status", "saved");
    }

    public function edit($id){
        $product = Type::findOrFail($id);
        return view("pages.Types.edit", compact("product"));
    }

    public function update(Request $request, $id){
        $product = Type::findOrFail($id);
        // validasai
        $request->validate([
            "code" => "required|unique:products,code,".$id,
            "name" => "required",
        ]);

            $product->code = strtolower($request->code);
            $product->name = strtolower($request->name);
            $product->save();

        return redirect()->route("jenis.index")->with("status", "edited");
    }

    public function destroy($id){
        Type::findOrFail($id)->delete();
        return redirect()->back()->with("status", "deleted");
    }
}
