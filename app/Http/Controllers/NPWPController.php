<?php

namespace App\Http\Controllers;

use App\Imports\NPWPImport;
use App\Models\npwp;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'name' => 'required',
        ]);

        npwp::create($request->all());
        return redirect()->route('npwp.index')->with('status', 'saved');
    }

    public function edit($id){
        $data = npwp::findOrFail($id);
        return view('pages.npwp.edit', compact(['data']));
    }

    public function update(Request $request, $id){
        $data = npwp::findOrFail($id);
        $request->validate([
            'npwp' => 'required',
            'name' => 'required',
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

    public function importNpwp(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $import = new NPWPImport();

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            // Tangani exception jika ada kesalahan
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        // Ambil pesan error jika ada
        $errors = $import->getErrors();

        if (!empty($errors)) {
            return redirect()->back()->with('import_errors', $errors);
        }

        return redirect()->back()->with('status', 'importSuccess');
    }
}
