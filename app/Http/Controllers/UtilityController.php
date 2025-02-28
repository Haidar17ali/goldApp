<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\PO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UtilityController extends Controller
{
    public function approve($modelType, $id, $status){
        $modelClass = 'App\Models\\' . ucfirst($modelType);
        
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }
        
        $model = $modelClass::findOrFail($id);
        
        if ($model->status !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya data pending yang bisa disetujui');
        }
        
        // Khusus untuk Purchase Order: Nonaktifkan data lama dengan supplier yang sama
        // if ($model instanceof PO) {
        //     $modelClass::where('supplier_id', $model->supplier_id)
        //     ->where('id', '!=', $model->id)
        //     ->where('status', 'approved')
        //     ->update(['status' => 'Non-Aktif']);
        // }

        $model->update([
            'status' => $status == 'Tidak Disetujui'? $status : "Pending",
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('status', $status);
    }

    public function activation($modelType, $id, $status){
        $modelClass = 'App\Models\\' . ucfirst($modelType);
        
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }
        
        $model = $modelClass::findOrFail($id);
        
        if ($model->status !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya data pending yang bisa disetujui');
        }
        
        // Khusus untuk Purchase Order: Nonaktifkan data lama dengan supplier yang sama
        if ($model instanceof PO) {
            $modelClass::where('supplier_id', $model->supplier_id)
            ->where('id', '!=', $model->id)
            ->where('status', 'Aktif')
            ->update(['status' => 'Non-Aktif']);
        }

        $model->update([
            'status' => $status,
        ]);

        return redirect()->back()->with('status', $status);
    }

    // ajax
    public function getNumberAccount(Request $request){
        $response = Bank::where("id", $request->id)->first();
        return response()->json($response);
    }

    // get npwp
    public function getByID(Request $request){
        $modelClass = 'App\Models\\' . ucfirst($request->model);
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }

        $response = $modelClass::findOrFail($request->id);

        if($request->relation){
            $response = $modelClass::with('npwp')->findOrFail($request->id);
        }
        
        return response()->json($response);
    }
}
