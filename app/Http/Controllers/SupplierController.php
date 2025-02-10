<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Bank;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(){
        $suppliers = Supplier::orderBy('id', 'desc')->with(['address', 'bank'])->get();
        return view('pages.suppliers.index', compact(['suppliers']));
    }
    
    public function create(){
        $banks = Bank::orderBy("id", 'desc')->get();
        $addresses = Address::orderBy("id", 'desc')->get();
        $types = ['Sengon', 'Merbau', 'Pembantu'];
        return view('pages.suppliers.create', compact(['addresses', 'banks', 'types']));
    }

    public function store(Request $request){
        $request->validate([
            'npwp_number' => 'required',
            'nitku' => 'required',
            'nik' => 'required',
            'type' => 'required|in:Sengon,Merbau,Pembantu',
            'name' => 'required',
        ]);

        // inisialisasi
        $npwp_number = $request->npwp_number;
        $nitku = $request->nitku;
        $nik = $request->nik;
        $type = $request->type;
        $name = $request->name;
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
            'npwp_number' => $npwp_number,
            'nitku' => $nitku,
            'nik' => $nik,
            'supplier_type' => $type,
            'name' => $name,
            'address_id' => $id_address->id,
            'bank_id' => $bank_id->id,
        ];

        Supplier::create($dataSupplier);
        return redirect()->route('supplier.index')->with('status', 'saved');
    }
}
