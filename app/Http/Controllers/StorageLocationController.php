<?php
namespace App\Http\Controllers;

use App\Models\StorageLocation;
use Illuminate\Http\Request;

class StorageLocationController extends BaseController
{
    public function index()
    {
        $locations = StorageLocation::orderBy('id', 'desc')->get();
        return view('pages.storage-locations.index', compact('locations'));
    }

    public function create()
    {
        return view('pages.storage-locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:storage_locations,name',
            'description' => 'nullable|string',
        ]);

        StorageLocation::create($request->all());

        return redirect()->route('penyimpanan.index')->with('status', 'saved');
    }

    public function edit($id)
    {
        $location = StorageLocation::findOrFail($id);
        return view('pages.storage-locations.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $location = StorageLocation::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:storage_locations,name,' . $location->id,
            'description' => 'nullable|string',
        ]);

        $location->update($request->all());

        return redirect()->route('penyimpanan.index')->with('status', 'updated');
    }

    public function destroy($id)
    {
        $location = StorageLocation::findOrFail($id);
        $location->delete();

        return redirect()->route('penyimpanan.index')->with('status', 'deleted');
    }
}
