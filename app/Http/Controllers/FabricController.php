<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use Illuminate\Http\Request;

class FabricController extends Controller
{
    public function index()
    {
        return view('pages.fabrics.index');
    }

    public function create()
    {
        return view('pages.fabrics.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'material' => 'nullable|string|max:255',
            'unit'     => 'required|string|max:50',
        ]);

        Fabric::create($request->all());

        return redirect()->route('kain.index')->with('status', 'saved');
    }

    public function edit($id)
    {
        $fabric = Fabric::findOrFail($id);
        return view('pages.fabrics.edit', compact('fabric'));
    }

    public function update(Request $request, $id)
    {
        $fabric = Fabric::findOrFail($id);
        $request->validate([
            'name'     => 'required|string|max:255',
            'material' => 'nullable|string|max:255',
            'unit'     => 'required|string|max:50',
        ]);

        $fabric->update($request->all());

        return redirect()->route('kain.index')->with('status', 'edited');
    }

    public function destroy($id)
    {
        $fabric = Fabric::findOrFail($id);
        $fabric->delete();
        return redirect()->route('kain.index')->with('status', 'deleted');
    }
}
