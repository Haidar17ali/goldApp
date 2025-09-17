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
        
        $model->update([
            'status' => $status,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        // if ($model instanceof PO) {
        //     $modelClass::where('supplier_id', $model->supplier_id)
        //     ->where('id', '!=', $model->id)
        //     ->update([
        //         'status' => 'Pending',
        //         'approved_by' => Auth::id(),
        //         'approved_at' => now(),
        //     ]);
        // }
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

    public function getMultipleData(Request $request){
        $modelClass = 'App\Models\\' . ucfirst($request->model);
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan');
        }
        
        $response = $modelClass::whereIn('id', $request->id ?? [])->get();
        
        if($request->relation){
            $response = $modelClass::with($request->relation ?? [])
            ->whereIn('id', $request->id ?? [])
            ->get();
        }

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
        'users' => [
            'model' => 'App\\Models\\User',
            'columns' => [
                'id',
                'username',
                'email',
                'is_active',
            ],
            'relations' => []
        ],
        'products' => [
            'model' => 'App\\Models\\Product',
            'columns' => [
                'id',
                'code',
                'name',
            ],
            'relations' => []
        ],
        'colors' => [
            'model' => 'App\\Models\\Color',
            'columns' => [
                'id',
                'code',
                'name',
            ],
            'relations' => []
        ],
        'sizes' => [
            'model' => 'App\\Models\\Size',
            'columns' => [
                'id',
                'code',
                'name',
                'width',
                'length',
            ],
            'relations' => []
        ],
        'cuttings' => [
            'model' => 'App\\Models\\Cutting',
            'columns' => [
                'id',
                'code',
                'date',
                'tailor_name',
                'create_by',
                'edit_by',
            ],
            'relations' => [
                'details.product'=> ["name","code"],
                'details.color'=> ["name","code"],
                'details.size'=> ["name","code"],
                'createBy'=> ["username"],
                'editBy'=> ["username"],
            ]
        ],
        'rotary' => [
            'model' => 'App\\Models\\Rotary',
            'columns' => [
                        'id',
                        'date',
                        'shift',
                        'wood_type',
                        'tally_id',
                        "created_by",
                        "edited_by"
                    ],
            'relations' => [
                'createdBy' => ['username'],
                'editedBy' => ['username'],
                'details' => ['no_kitir'],
                'rotariSources' => ['rotary_id'],
            ]
        ],
        'wood-management' => [
            'model' => 'App\\Models\\WoodManagement',
            'columns' => [
                        'id',
                        "date",
                        "no_kitir",
                        "grade",
                        "type",
                        "from",
                        "to",
                        "tally_id",
                        "created_by",
                        "edited_by"
                    ],
            'relations' => []
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
        $type = $request->input('type');
        $isEdit = $request->edit;
        $page = $request->page ?? 1;
        
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

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($modelClass, $columns, $relations, $search, $withRelations, $type) {
            return App::make($modelClass)::select($columns)->with($withRelations)
            ->when(in_array('parent_id', $columns), function ($query) {
                $query->whereNull('parent_id');
            })
            ->where(function($q) use($columns, $relations, $search){
                foreach($columns as $column){

                    if ($column === 'date' || $column === 'datetime') {
                        $q->orWhereRaw("DATE_FORMAT($column, '%d-%m-%Y') LIKE ?", ["%$search%"]);
                    }else {
                        $q->orWhere($column, 'like', "%$search%");
                    }
                }
                foreach($relations as $relation => $fields){
                    $q->orWhereHas($relation, function($query) use ($fields,$search){
                        if($fields){
                            foreach($fields as $field){
                                $query->where($field, 'like', "%$search%");
                            }
                        }
                    });
                }
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderBy("id", "desc")->paginate(10);

        });

        if($modelKey  == "products"){
            return response()->json([
                'table' => view('pages.search.search-product', compact('data'))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "colors"){
            return response()->json([
                'table' => view('pages.search.search-color', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "users"){
            return response()->json([
                'table' => view('pages.search.search-user', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "sizes"){
            return response()->json([
                'table' => view('pages.search.search-size', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "cuttings"){
            return response()->json([
                'table' => view('pages.search.search-cutting', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }

    }

    
}
