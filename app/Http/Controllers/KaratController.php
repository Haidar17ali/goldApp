<?php

namespace App\Http\Controllers;

use App\Models\Karat;
use Illuminate\Http\Request;

class KaratController extends BaseController
{
    public function index()
    {
        return view("pages.karat.index");
    }

    public function create()
    {
        return view("pages.karat.create");
    }

    public function store(Request $request)
    {
        // validasai
        $request->validate([
            "name" => "required",
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data = [
            "name" => strtolower($request->name),
            "percentage" => strtolower($request->percentage),
        ];

        Karat::create($data);
        return redirect()->route("karat.index")->with("status", "saved");
    }

    public function edit($id)
    {
        $karat = Karat::findOrFail($id);
        return view("pages.karat.edit", compact("karat"));
    }

    public function update(Request $request, $id)
    {
        $Karat = Karat::findOrFail($id);
        // validasai
        $request->validate([
            "name" => "required",
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $Karat->name = strtolower($request->name);
        $Karat->percentage = strtolower($request->percentage);
        $Karat->save();

        return redirect()->route("karat.index")->with("status", "edited");
    }

    public function destroy($id)
    {
        Karat::findOrFail($id)->delete();
        return redirect()->back()->with("status", "deleted");
    }
}
