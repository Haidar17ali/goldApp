<?php

namespace App\Http\Controllers;

use App\Models\CustomerSupplier;
use Illuminate\Http\Request;

class CustomerSupplierController extends BaseController
{
    public function index($type){
        return view("pages.customer-suppliers.index", compact("type"));
    }

    public function create($type)
    {
        return view('pages.customer-suppliers.create', compact("type"));
    }

    public function store(Request $request, $type)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama wajib diisi',
        ]);

        // Ambil tipe dari URL parameter
        $validated['type'] = $type;

        // Simpan data
        CustomerSupplier::create($validated);

        return redirect()
            ->route('customer-supplier.index', $type)
            ->with('status', 'saved');
    }

     public function edit($type, $id)
    {
        $item = CustomerSupplier::where('type', $type)->findOrFail($id);
        return view('pages.customer-suppliers.edit', compact('item', 'type'));
    }

    public function update(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:500',
        ]);

        $item = CustomerSupplier::where('type', $type)->findOrFail($id);
        $item->update($validated);

        return redirect()
            ->route('customer-supplier.index', $type)
            ->with('status', 'updated');
    }

    public function destroy($type, $id)
    {
        $item = CustomerSupplier::where('type', $type)->findOrFail($id);
        $item->delete();

        return redirect()
            ->route('customer-supplier.index', $type)
            ->with('status', 'deleted');
    }

}
