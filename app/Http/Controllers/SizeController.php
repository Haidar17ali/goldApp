<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(){
        return view("pages.sizes.index");
    }

    public function create(){
        return view("pages.sizes.create");
    }

    public function store(Request $request){
        // validasai
        $request->validate([
            "code" => "unique:products|required",
            "name" => "required",
            "width" => "nullable|numeric",
            "length" => "nullable|numeric",
        ]);

        $data = [
            "code"=> strtolower($request->code),
            "name"=> strtolower($request->name),
            "width"=> $request->width??null,
            "length"=> $request->length??null,
        ];

        Size::create($data);
        return redirect()->route("size.index")->with("status", "saved");
    }

    public function edit($id){
        $size = Size::findOrFail($id);
        return view("pages.sizes.edit", compact("size"));
    }

    public function update(Request $request, $id){
        $size = Size::findOrFail($id);
        // validasai
        $request->validate([
            "code" => "required|unique:products,code,".$id,
            "name" => "required",
        ]);

            $size->code = strtolower($request->code);
            $size->name = strtolower($request->name);
            $size->width = $request->width;
            $size->length = $request->length;
            $size->save();

        return redirect()->route("size.index")->with("status", "edited");
    }

    public function destroy($id){
        Size::findOrFail($id)->delete();
        return redirect()->back()->with("status", "deleted");
    }
}
