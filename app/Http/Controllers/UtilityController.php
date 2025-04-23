<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Down_payment;
use App\Models\LPB;
use App\Models\PO;
use App\Models\PurchaseJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class UtilityController extends Controller
{
    public function approve($modelType, $id, $status){
        $modelClass = 'App\Models\\' . ucfirst($modelType);
        
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }
        
        $model = $modelClass::findOrFail($id);
        
        if ($model->status !== 'Pending' && $model->status !== 'Terbayar') {
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
            'status' => $status,
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
        
        // if ($model->status !== 'Pending') {
        //     return redirect()->back()->with('error', 'Hanya data pending yang bisa disetujui');
        // }
        
        // Khusus untuk Purchase Order: Nonaktifkan data lama dengan supplier yang sama
        // if ($model instanceof PO) {
        //     $modelClass::where('supplier_id', $model->supplier_id)
        //     ->where('id', '!=', $model->id)
        //     ->where('status', 'Aktif')
        //     ->update(['status' => 'Non-Aktif']);
        // }
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

    public function getByType(Request $request) {
        $modelClass = 'App\Models\\' . ucfirst($request->model);
        $isEdit = $request->isEdit;
        $pjId = $request->pj_id;
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }
        
        if ($request->model ==  'Down_payment'){
            $response = $modelClass::where('status', $request->type)->where('pj_id', null)->get();
            
            if($request->relation){
                $response = $modelClass::with($request->relation)->where('status', $request->type)->where('pj_id', null)->get();
            }

            if($isEdit){
                $response = $modelClass::where('status', $request->type)->where('pj_id', null)->orwhere('pj_id', $pjId)->get();
    
                if($request->relation){
                    $response = $modelClass::with($request->relation)->where('status', $request->type)->where('pj_id', null)->orwhere('pj_id', $pjId)->get();
                }
            }
        }
        
        return response()->json($response);
    }

    private $allowedModels = [
        'down_payments' => [
            'model' => 'App\\Models\\Down_payment',
            'columns' => [
                'supplier_id',
                'nominal',
                'date',
                'type',
                'status'
            ],
            'relations' => [
                'supplier' => ['name']
            ]
        ],
        'purchase_orders' => [
            'model' => 'App\\Models\\PO',
            'columns' => [
                'po_date',
                'arrival_date',
                'payment_date',
                'po_code',
                'po_type',
                'supplier_id',
                'supplier_type',
                'ppn',
                'dp',
                'status',
                'order_by',
                'created_by',
                'edited_by',
                'edited_by',
                'approved_by',
                'approved_at',
                'activation_date',
            ],
            'relations' => [
                'supplier' => ['name'],
                'createdBy' => ['username'],
                'order_by' => ['fullname'],
                'approvedBy' => ['username'],
            ]
        ],
        'lpbs' => [
            'model' => 'App\\Models\\LPB',
            'columns' => [
                'code',
                'po_id',
                'road_permit_id',
                'no_kitir',
                'nopol',
                'lpb_date',
                'supplier_id',
                'npwp_id',
                'grader_id',
                'tally_id',
                'used',
                'used_at',
                'perhutani',
                'created_by',
                'edited_by',
                'approved_by',
                'approved_at',
                'conversion',
                'status',
                'address_id',
            ],
            'relations' => [
                'supplier' => ['name'],
                'createdBy' => ['username'],
            ]
        ],
        'road_permits' => [
            'model' => 'App\\Models\\RoadPermit',
            'columns' => [
                'code',
                'date',
                'in',
                'out',
                'description',
                'handyman_id',
                'from',
                'destination',
                'vehicle',
                'nopol',
                'driver',
                'unpack_location',
                'sill_number',
                'container_number',
                'type',
                'type_item',
                'created_by',
                'edited_by',
            ],
            'relations' => [
                'supplier' => ['name'],
                'createdBy' => ['username'],
                'handyman' => ['fullname'],
            ]
        ],
        'purchase_jurnals' => [
            'model' => 'App\\Models\\PurchaseJurnal',
            'columns' => [
                'id',
                'pj_code',
                'date',
                'created_by',
                'edited_by',
                'status',
            ],
            'relations' => [
                'createdBy' => ['username'],
            ]
        ],
    ];

    public function search(Request $request){
        $search = $request->input('search');
        $from = $request->input('from');
        $modelKey = $request->input('model');
        $isEdit = $request->edit;
        $page = $request->page ?? 1;

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

            if($isEdit){
                $pj = PurchaseJurnal::with([
                    'details.lpbs.details' // untuk ambil detail kayu
                ])->findOrFail($request->idPurchase);

                $lpbPurchases = $pj->details->pluck('lpbs')->unique('id')->values()[0];
                $lpbPurchaseIds = $lpbPurchases->pluck('id')->toArray();

                $lpbs = Lpb::with(['supplier', 'details'])
                ->where('status', 'Pending') // Pindahkan kondisi status di awal
                ->orWhereIn('id', $lpbPurchaseIds)
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
            }

            return response()->json($lpbs);
        }
        
        // Validasi model
        if (!isset($this->allowedModels[$modelKey])) {
            return response()->json(['error' => 'Model tidak diizinkan'], 403);
        }
        
        $modelInfo = $this->allowedModels[$modelKey];
        $modelClass = $modelInfo['model'];
        $columns = $request->input('columns', []); // Ambil dari frontend, default array kosong
        $relations = $request->input('relations', []); // Relasi dari frontend jika ada

        // Jika frontend tidak mengirimkan kolom, gunakan default dari backend
        if (empty($columns)) {
            $columns = $modelInfo['columns'];
        }
        if(count($relations) == 0){
            $relations = $modelInfo['relations'];
        }
        
        $withRelations = [];
        if(count($relations)){
            foreach($relations as $index => $dataRelation){
                $withRelations[]= $index;
            }
        }
        
        // Caching untuk meningkatkan performa
        $cacheKey = "search_{$modelKey}_{$search}_page_{$page}";

        Cache::forget($cacheKey); // Hapus cache agar query tidak tersimpan

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($modelClass, $columns, $relations, $search, $withRelations) {
            return App::make($modelClass)::select($columns)->with($withRelations)
            ->where(function($q) use($columns, $relations, $search){
                foreach($columns as $column){
                    if ($column === 'date' || $column === 'datetime') {
                        $q->orWhereRaw("DATE_FORMAT($column, '%d-%m-%Y') LIKE ?", ["%$search%"]);
                    } else {
                        $q->orWhere($column, 'like', "%$search%");
                    }
                }
                foreach($relations as $relation => $fields){
                    $q->orWhereHas($relation, function($query) use ($fields,$search){
                        foreach($fields as $field){
                            $query->where($field, 'like', "%$search%");
                        }
                    });
                }
            })
            ->orderBy("id", "desc")->paginate(10);
        });

        if($modelKey  == "down_payments"){
            return response()->json([
                'table' => view('pages.search.search', compact('data'))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }else if($modelKey == "purchase_orders"){
            $type = $request->type;
            return response()->json([
                'table' => view('pages.search.search-po', compact(['data',"type"]))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }else if($modelKey == "lpbs"){
            $type = $request->type;
            return response()->json([
                'table' => view('pages.search.search-lpb', compact(['data',"type"]))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }else if($modelKey == "road_permits"){
            $type = $request->type;
            return response()->json([
                'table' => view('pages.search.search-RP', compact(['data',"type"]))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }else if($modelKey == "purchase_jurnals"){
            $data->each(function ($purchaseJurnal) {
                $purchaseJurnal->allLpbs = $purchaseJurnal->details->flatMap(function ($detail) {
                    return $detail->lpbs;
                });
                
                $failedLpbs = [];
    
                foreach ($purchaseJurnal->details as $detail) {
                    foreach ($detail->lpbs as $lpb) {
                        if ($lpb->pivot->status === 'Gagal') {
                            $failedLpbs[] = $lpb;
                        }
                    }
                }
                $purchaseJurnal->failedLpbs = $failedLpbs;
            });

            return response()->json([
                'table' => view('pages.search.search-PJ', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }

    }

    
}
