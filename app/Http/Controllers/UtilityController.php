<?php

namespace App\Http\Controllers;

use App\Models\PO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UtilityController extends Controller
{
    public function approve($modelType, $id, $status){
        $modelClass = 'App\Models\\' . ucfirst($modelType);
        dd($modelClass);

        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }

        $model = $modelClass::findOrFail($id);

        if ($model->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya data pending yang bisa disetujui');
        }

        // Khusus untuk Purchase Order: Nonaktifkan data lama dengan supplier yang sama
        if ($model instanceof PO) {
            $modelClass::where('supplier_id', $model->supplier_id)
                ->where('id', '!=', $model->id)
                ->where('status', 'approved')
                ->update(['status' => 'inactive']);
        }

        $model->update([
            'status' => $status,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', class_basename($model) . ' approved successfully');
    }
}
