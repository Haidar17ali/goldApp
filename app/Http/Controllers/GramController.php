<?php

namespace App\Http\Controllers;

use App\Models\Gram;
use Illuminate\Http\Request;

class GramController extends Controller
{
    public function index(){
        return view("pages.grams.index");
    }

    public function create(){
        return view("pages.grams.create");
    }

    public function store(Request $request){
        // validasai
        $request->validate([
            "name" => "required",
            "weight" => "required"
        ]);

        $data = [
            "name"=> strtolower($request->name),
            "weight"=> $request->weight,
        ];

        Gram::create($data);
        return redirect()->route("berat.index")->with("status", "saved");
    }

    public function edit($id){
        $gram = Gram::findOrFail($id);
        return view("pages.grams.edit", compact("gram"));
    }

    public function update(Request $request, $id){
        $gram = Gram::findOrFail($id);
        // validasai
        $request->validate([
            "name" => "required",
            "weight" => "required"
        ]);

            $gram->name = strtolower($request->name);
            $gram->weight = $request->weight;
            $gram->save();

        return redirect()->route("berat.index")->with("status", "edited");
    }

    public function destroy($id){
        Gram::findOrFail($id)->delete();
        return redirect()->back()->with("status", "deleted");
    }
}
