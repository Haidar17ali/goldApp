<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use App\Imports\SupplierImport;
use App\Models\Address;
use App\Models\Bank;
use App\Models\npwp;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index(){
        $suppliers = Supplier::orderBy('id', 'desc')->with(['address', 'bank'])->get();
        return view('pages.suppliers.index', compact(['suppliers']));
    }
    
    public function create(){
        $banks = Bank::orderBy("id", 'desc')->get();
        $addresses = Address::orderBy("id", 'desc')->get();
        $npwps = npwp::all();
        $types = ['Sengon', 'Merbau', 'Pembantu'];
        return view('pages.suppliers.create', compact(['addresses', 'banks', 'types','npwps']));
    }

    public function store(Request $request){
        $request->validate([
            'type' => 'required|in:Sengon,Merbau,Pembantu',
            'name' => 'required',
            'npwp' => 'required',
            'number_account' => 'required|numeric',
            'address' => 'required',
        ]);

        if($request->address == "null"){
            return redirect()->back()->with('status', 'required_address');
        }

        if($request->phone != null){
            $request->validate([
                'phone' => 'numeric'
            ]);
        }

        // inisialisasi
        $npwp_id = $request->npwp;
        $nik = $request->nik;
        $type = $request->type;
        $name = $request->name;
        $phone = $request->phone;
        $address = $request->address;
        $rt = $request->rt;
        $rw = $request->rw;
        $kelurahan = $request->kelurahan;
        $kecamatan = $request->kecamatan;
        $city = $request->city;
        $bank_name = $request->bank_name;
        $bank_account = $request->bank_account;
        $number_account = $request->number_account;
        
        $id_address = 0;
        // ambil data dari db berdasarkan alamat yg di input
        $db_address = Address::where('id', $address)->orWhere('address', $address)->where('rt', $rt)->where('rw', $rw)->where('city', $city)->first();
        // cek alamat apakah alamat sudah ada kalau tidak ada bikin baru
        if($db_address == null){
            $data = [
                'address' => $address,
                'rt' => (int)$rt,
                'rw' => (int)$rw,
                'kelurahan' => $kelurahan,
                'kecamatan' => $kecamatan,
                'city' => $city,
            ];
            $id_address = Address::create($data);
        }else{
            $id_address = $db_address;
        }

        $bank_id = 0;
        $db_bank = Bank::where('id', $number_account)->orWhere('number_account', $number_account)->first();
        if($db_bank == null){
            $bankData = [
                "bank_name" => $bank_name,
                "bank_account" => $bank_account,
                "number_account" => $number_account,
            ];
            $bank_id = Bank::create($bankData);
        }else{
            $bank_id = $db_bank;
        }
        
        $dataSupplier = [
            'npwp_id' => $npwp_id,
            'nik' => $nik,
            'supplier_type' => $type,
            'name' => $name,
            'phone' => $phone,
            'address_id' => $id_address->id,
            'bank_id' => $bank_id->id,
        ];

        Supplier::create($dataSupplier);
        return redirect()->route('supplier.index')->with('status', 'saved');
    }

    public function edit($id){
        $supplier = Supplier::with(['bank', 'address'])->findOrFail($id);
        $banks = Bank::orderBy("id", 'desc')->get();
        $addresses = Address::orderBy("id", 'desc')->get();
        $npwps = npwp::all();
        $types = ['Sengon', 'Merbau', 'Pembantu'];
        
        return view('pages.suppliers.edit', compact(['addresses', 'banks', 'types', 'supplier','npwps']));
    }

    public function update(Request $request, $id){
        $supplier = Supplier::with(['bank', 'address'])->findOrFail($id);

        $request->validate([
            'type' => 'required|in:Sengon,Merbau,Pembantu',
            'name' => 'required',
            'npwp' => 'required',
        ]);

        if($request->phone != null){
            $request->validate([
                'phone' => 'numeric'
            ]);
        }

        // inisialisasi
        $npwp_id = $request->npwp;
        $nik = $request->nik;
        $type = $request->type;
        $name = $request->name;
        $phone = $request->phone;
        $address = $request->address;
        $rt = $request->rt;
        $rw = $request->rw;
        $kelurahan = $request->kelurahan;
        $kecamatan = $request->kecamatan;
        $city = $request->city;
        $bank_name = $request->bank_name;
        $bank_account = $request->bank_account;
        $number_account = $request->number_account;
        
        $id_address = 0;
        // ambil data dari db berdasarkan alamat yg di input
        $db_address = Address::where('id', $address)->orWhere('address', $address)->where('rt', $rt)->where('rw', $rw)->where('city', $city)->first();
        // cek alamat apakah alamat sudah ada kalau tidak ada bikin baru
        if($db_address == null){
            $data = [
                'address' => $address,
                'rt' => (int)$rt,
                'rw' => (int)$rw,
                'kelurahan' => $kelurahan,
                'kecamatan' => $kecamatan,
                'city' => $city,
            ];
            $id_address = Address::create($data);
        }else{
            $id_address = $db_address;
        }

        $bank_id = 0;
        $db_bank = Bank::where('id', $number_account)->orWhere('number_account', $number_account)->first();
        if($db_bank == null){
            $bankData = [
                "bank_name" => $bank_name,
                "bank_account" => $bank_account,
                "number_account" => $number_account,
            ];
            $bank_id = Bank::create($bankData);
        }else{
            $bank_id = $db_bank;
        }
        
        $supplier->npwp_id = $npwp_id;
        $supplier->nik = $nik;
        $supplier->supplier_type = $type;
        $supplier->name = $name;
        $supplier->phone = $phone;
        $supplier->address_id = $id_address->id;
        $supplier->bank_id = $bank_id->id;
        $supplier->save();

        return redirect()->route('supplier.index')->with('status', 'edited');
    }

    public function destroy($id){
        Supplier::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'deleted');
    }

    public function importSupplier(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $import = new SupplierImport();

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
