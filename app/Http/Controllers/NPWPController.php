<?php

namespace App\Http\Controllers;

use App\Models\npwp;
use Illuminate\Http\Request;

class NPWPController extends Controller
{
    public function index(){
        $datas = npwp::paginate(10);
        return view('pages.npwp.index', compact(['datas']));
    }

    public function create(){
        return view('pages.npwp.create');
    }

    public function store(Request $request){
        $request->validate([
            'npwp' => 'required',
            'name' => 'required'
        ]);

        npwp::create($request->all());
        return redirect()->route('npwp.index')->with('status', 'saved');
    }

    public function edit($id){
        $data = npwp::findOrFail($id);
        return view('pages.npwp.edit', compact('data'));
    }

    public function update(Request $request, $id){
        $data = npwp::findOrFail($id);
        $request->validate([
            'npwp' => 'required',
            'name' => 'required'
        ]);

        $data->npwp = $request->npwp;
        $data->nitku = $request->nitku;
        $data->name = $request->name;
        $data->save();

        return redirect()->route('npwp.index')->with('status', 'edited');
    }

    public function destroy($id){
        npwp::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'deleted');
    }
}
