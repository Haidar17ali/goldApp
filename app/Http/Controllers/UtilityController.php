<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\LPB;
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
            $response = $modelClass::with($request->relation)->findOrFail($request->id);
        }
        
        return response()->json($response);
    }

    public function search(Request $request){
        // $query = LPB::query();

        // if ($request->has('search')) {
        //     $search = $request->search;
        //     if($request->from == "LPB"){
        //         $query->where('code', 'like', '%' . $search . '%')
        //             ->orWhere('nopol', 'like', '%' . $search . '%')
        //             ->orWhere('no_kitir', 'like', '%' . $search . '%')->orWhereHas('supplier', function ($q) use ($search) {
        //                 $q->where('name', 'like', "%$search%");
        //             })
        //             ->where('status', 'Pending')
        //             ->where('approved_by', '!=', null);
        //     }
        // }

        // return response()->json($query->with(['supplier', 'details', 'roadPermit'])->get());
        $search = $request->input('search');
        $from = $request->input('from');

        if ($from === 'LPB') {
            $lpbs = Lpb::with(['supplier', 'details'])
            ->where('status', 'Pending') // Pindahkan kondisi status di awal
            ->where('approved_by', '!=', null)
            ->where(function ($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%')
                    ->orWhere('nopol', 'like', '%' . $search . '%')
                    ->orWhere('no_kitir', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            })
            ->get();

            $lpbs->each(function ($lpb) {
                if ($lpb->supplier) {
                    $lpb->supplier->sisaDp = $lpb->supplier->sisaDp();
                }
            });

            return response()->json($lpbs);
        }
    }
}
