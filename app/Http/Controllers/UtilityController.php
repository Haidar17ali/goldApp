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

class UtilityController extends BaseController
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
        'fabrics' => [
            'model' => 'App\\Models\\Fabric',
            'columns' => [
                'id',
                'name',
                'material',
                'unit',
            ],
            'relations' => []
        ],
        'customerSuppliers' => [
            'model' => 'App\\Models\\CustomerSupplier',
            'columns' => [
                'id',
                'name',
                'phone_number',
                'address',
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
        'types' => [
            'model' => 'App\\Models\\Type',
            'columns' => [
                'id',
                'code',
                'name',
            ],
            'relations' => []
        ],
        'karats' => [
            'model' => 'App\\Models\\Karat',
            'columns' => [
                'id',
                'name',
            ],
            'relations' => []
        ],
        'branches' => [
            'model' => 'App\\Models\\Branch',
            'columns' => [
                'id',
                'code',
                'name',
                'address',
            ],
            'relations' => []
        ],
        'storageLocations' => [
            'model' => 'App\\Models\\storageLocation',
            'columns' => [
                'id',
                'name',
                'description',
            ],
            'relations' => []
        ],
        'productVariants' => [
            'model' => 'App\\Models\\ProductVariant',
            'columns' => [
                'id',
                'product_id',
                'karat_id',
                'gram',
                'sku',
                'barcode',
                'default_price',
            ],
            'relations' => [
                'product'=> ["name","code"],
                'karat'=> ["name"],
            ]
        ],
        'bankAccounts' => [
            'model' => 'App\\Models\\BankAccount',
            'columns' => [
                'id',
                'bank_name',
                'account_number',
                'account_holder',
                'is_active',
            ],
            'relations' => []
        ],
        'transactions' => [
            'model' => 'App\\Models\\Transaction',
            'columns' => [
                'id',
                'transaction_date',
                'invoice_number',
                'total',
                'customer_name',
                'supplier_name',
                'note',
                'created_by',
            ],
            'relations' => [
                'user'=> ["username"],
            ]
        ],
        'sales' => [
            'model' => 'App\\Models\\Transaction',
            'columns' => [
                'id',
                'transaction_date',
                'invoice_number',
                'total',
                'customer_name',
                'supplier_name',
                'note',
                'created_by',
            ],
            'relations' => [
                'user'=> ["username"],
            ]
        ],
        'goldConversions' => [
            'model' => 'App\\Models\\GoldConversion',
            'columns' => [
                'id',
                'stock_id',
                'karat_id',
                'input_weight',
                'total_output_weight',
                'loss_weight',
                'note',
                'created_by',
                'edited_by'
            ],
            'relations' => [
                'product'=> ["username"],
                'kadar'=> ["username"],
            ]
        ],
        'goldMergeConversions' => [
            'model' => 'App\\Models\\GoldMergeConversion',
            'columns' => [
                'id',
                'note',
                'created_by',
                'edited_by',
            ],
            'relations' => []
        ],
        'stockAdjustments' => [
            'model' => 'App\\Models\\StockAdjustment',
            'columns' => [
                'id',
                'branch_id',
                'storage_location_id',
                'adjustment_date',
                'note',
                'created_by',
                'approved_by',
                'approved_at'
            ],
            'relations' => []
        ],
    ];

    public function search(Request $request){
        $search = $request->input('search');
        $from = $request->input('from');
        $modelKey = $request->input('model');
        $type = $request->input('type');
        $purchaseType = $request->input('purchase_type');
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

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($modelClass, $columns, $relations, $search, $withRelations, $type,$purchaseType) {
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
            ->when($purchaseType, function ($query) use ($purchaseType) {
                $query->where('purchase_type', $purchaseType);
            })
            ->orderBy("id", "desc")->paginate(10);

        });

        if($modelKey  == "fabrics"){
            return response()->json([
                'table' => view('pages.search.search-fabrics', compact('data'))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "customerSuppliers"){
           return response()->json([
                'table' => view('pages.search.search-customer-supliers', compact(['data', 'type']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "products"){
           return response()->json([
                'table' => view('pages.search.search-product', compact('data'))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "types"){
            return response()->json([
                'table' => view('pages.search.search-type', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "users"){
            return response()->json([
                'table' => view('pages.search.search-user', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "karats"){
            return response()->json([
                'table' => view('pages.search.search-karat', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "branches"){
            return response()->json([
                'table' => view('pages.search.search-branch', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "storageLocations"){
            return response()->json([
                'table' => view('pages.search.search-storageLocation', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "productVariants"){
            return response()->json([
                'table' => view('pages.search.search-product-variant', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "bankAccounts"){
            return response()->json([
                'table' => view('pages.search.search-bank-accounts', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "transactions"){
            return response()->json([
                'table' => view('pages.search.search-transactions', compact(['data', "type", "purchaseType"]))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "sales"){
            return response()->json([
                'table' => view('pages.search.search-sales', compact(['data', "type"]))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "goldConversions"){
            return response()->json([
                'table' => view('pages.search.search-gold-conversions', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "goldMergeConversions"){
            return response()->json([
                'table' => view('pages.search.search-gold-merge-conversions', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }elseif($modelKey == "stockAdjustments"){
            return response()->json([
                'table' => view('pages.search.search-stock-adjustments', compact(['data']))->render(),
                'pagination' => view('vendor/pagination/bootstrap-4',['paginator' => $data])->render(),
            ]);
        }

    }

    
}
