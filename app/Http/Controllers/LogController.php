<?php

namespace App\Http\Controllers;

use App\Imports\LogImport;
use App\Models\Log;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogController extends Controller
{
    public function index($type){
        $logs = Log::where('type', $type)->paginate(20);
        return view('pages.logs.index', compact(['logs', 'type']));
    }

    public function create($type){
        $qualities = ['Super', 'Afkir'];
        return view('pages.logs.create', compact(['qualities', 'type']));
    }

    public function store(Request $request, $type){
        $request->validate([
            'quality' => 'required|in:Super,Afkir',
            'length' => 'required|decimal:0,4',
            'diameter' => 'required|decimal:0,4',
            'quantity' => 'required|decimal:0,4',
        ]);

        $code = 0;
        if($type == 'Sengon'){
            $code = 'SGN.'. substr($request->quality, 0,2).'.'.$request->length.'.'.$request->diameter;
        }else{
            $request->validate([
                'id_produksi' => 'required',
                'barcode' => 'required'
            ]);
            $code = 'MBU.'. substr($request->quality, 0,2).'.'.$request->length.'.'.$request->diameter;
        }

        $db_code = Log::where('code', $code)->where('type', $type)->first();

        if($db_code){
            $db_code->quantity += $request->quantity;
            $db_code->save();
        }else{
            $data = [
                'id_produksi' => $request->id_produksi,
                'barcode' => $request->barcode,
                'code' => $code,
                'type' => $type,
                'quality' => $request->quality,
                'length' => $request->length,
                'diameter' => $request->diameter,
                'quantity' => $request->quantity,
            ];
    
            Log::create($data);
        }
        return redirect()->route('log.index', $type)->with('status', 'saved');

    }

    public function edit($id,$type){
        $log = Log::findOrFail($id);
        $qualities = ['Super', 'Afkir'];
        return view('pages.logs.edit', compact(['qualities', 'type', 'log']));
    }

    public function update(Request $request, $id, $type){
        $log = Log::findOrFail($id);
        $request->validate([
            'quality' => 'required|in:Super,Afkir',
            'length' => 'required|decimal:0,4',
            'diameter' => 'required|decimal:0,4',
            'quantity' => 'required|decimal:0,4',
        ]);

        $code = 0;
        if($type == 'Sengon'){
            $code = 'SGN.'. substr($request->quality, 0,2).'.'.$request->length.'.'.$request->diameter;
        }elseif($type == 'Merbau'){
            $request->validate([
                'id_produksi' => 'required',
                'barcode' => 'required'
            ]);
            $code = 'MBU.'. substr($request->quality, 0,2).'.'.$request->length.'.'.$request->diameter;
        }else{
            $code = $log->code;
        }

        $db_code = Log::where('code', $code)->where('type', $type)->first();

        if($db_code){
            $db_code->quantity += $request->quantity;
            $db_code->save();
        }else{
            $log->id_produksi = $request->id_produksi;
            $log->barcode = $request->barcode;
            $log->code = $code;
            $log->type = $type;
            $log->quality = $request->quality;
            $log->length = $request->length;
            $log->diameter = $request->diameter;
            $log->quantity = $request->quantity;
            $log->save();
        }


        return redirect()->route('log.index', $type)->with('status', 'edited');
    }

    public function destroy($id){
        Log::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'deleted');
    }
    
    public function importLog(Request $request, $type){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $import = new LogImport($type);

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
