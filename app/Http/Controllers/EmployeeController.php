<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use App\Models\Address;
use App\Models\Bank;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(){
        $employees = Employee::orderBy('fullname', 'asc')->with(['salary'])->paginate(15);
        return view('pages.employees.index', compact('employees'));
    }

    public function create(){
        $genders = [
                [
                    "value" => 1,
                    "name" => "L"
                ],
                [
                    "value" => 0,
                    "name" => "P"
                ]
        ];

        $statuses = [
                [
                    "value" => 1,
                    "name" => "Aktif"
                ],
                [
                    "value" => 0,
                    "name" => "Nonaktif"
                ]
        ];

        $mariage_statuses = [
            'Belum kawin',
            'Kawin belum tercatat', 
            'Kawin tercatat', 
            'Cerai hidup', 
            'Cerai mati'
        ];

        $locations = [
            'Bukir Utara',
            'Bukir Selatan',
            'Kaligung'
        ];

        $employee_types = [
            'Harian',
            'Bulanan',
            'Borongan'
        ];

        $payments = [
            'ATM',
            'Tunai',
        ];

        $addresses = Address::orderBy('id', 'desc')->get();
        $banks = Bank::orderBy('id', 'desc')->get();
        $positions = Position::where("type", "Bagian")->with(['parent.parent'])->get()->map(function($position){
            return [
                'id' => $position->id,
                'name' => $position->name,
                'grandparent' => optional(optional($position->parent)->parent)->name, // Pastikan tidak error jika null
            ];
        });

        return view('pages.employees.create', compact([
            'genders',
            'addresses', 
            'positions', 
            'locations',
            'payments',
            'mariage_statuses',
            'statuses',
            'employee_types',
            'banks'
        ]));
    }

    public function store(Request $request){
        // membuat validasi karyawan
        $request->validate([
            'nik' => 'required|numeric',
            'no_kk' => 'required|numeric',
            'fullname' => 'required',
            'alias_name' => 'required',
            'gender' => 'required|in:0,1',
            'mariage_status' => 'required|in:Belum kawin,Kawin belum tercatat,Kawin tercatat,Cerai hidup,Cerai mati',
            'family_depents' => 'required|in:0,1,2,3|numeric',
            'employee_type' => 'required|in:Harian,Bulanan',
            'position' => 'required|exists:positions,id',
            'entry_date' => 'required|date',
            'salary' => 'required|numeric',
            'location' => 'required|in:Bukir Utara,Bukir Selatan,Kaligung',
            'payment_type' => 'required|in:ATM,Tunai',
        ]);

        // inisialisasi
        $pin = $request->pin;
        $nik = $request->nik;
        $no_kk = $request->no_kk;
        $fullname = $request->fullname;
        $alias_name = $request->alias_name;
        $gender = $request->gender;
        $mariage_status = $request->mariage_status;
        $family_depents = $request->family_depents;
        $employee_type = $request->employee_type;
        $address = $request->address;
        $rt = $request->rt;
        $rw = $request->rw;
        $kelurahan = $request->kelurahan;
        $kecamatan = $request->kecamatan;
        $city = $request->city;
        $position = $request->position;
        $entry_date = $request->entry_date;
        $salary = $request->salary;
        $premi = $request->premi;
        $location = $request->location;
        $payment_type = $request->payment_type;
        $bank_name = $request->bank_name;
        $bank_account = $request->bank_account;
        $number_account = $request->number_account;
        $jkn_number = $request->jkn_number;
        $jkp_number = $request->jkp_number;
        $jkp_number = $request->jkp_number;
        $status = $request->status;



        // buat nip karyawan berdasarkan tanggal masuk
        // kode nip
        $kode = "";
        $max_code = DB::select("SELECT max(right(nip, 2)) as kode FROM employees WHERE entry_date LIKE '$entry_date%'");
        if (count($max_code) > 0) {
            foreach ($max_code as $q) {
                $no = ((int)$q->kode) + 1;
                $kd = sprintf("%02s", $no);
            }
        } else {
            $kd = "01";
        }
        $tanggal_masuk_code = str_replace('-', '', $entry_date);
        $kode = $tanggal_masuk_code . $kd;

        // cek gaji apakah di table gaji sudah ada kalau tidak ada bikin baru
        $id_salary = 0;
        $db_salary = Salary::where('salary', $salary)->first();
        if($db_salary == null){
            $id_salary = Salary::create(['salary' =>$salary]);
        }else{
            $id_salary = $db_salary->id;
        }

        $id_address = 0;
        // ambil data dari db berdasarkan alamat yg di input
        $db_address = Address::where('id', $address)->orWhere('address', $address)->where('rt', $rt)->where('rw', $rw)->where('city', $city)->first();
        // cek alamat apakah alamat sudah ada kalau tidak ada bikin baru
        if($db_address == null){
            if($address == null || $rt == null|| $rw == null|| $kelurahan == null|| $kecamatan == null || $city == null){
                return redirect()->back()->with('status', 'addressErr');
            }
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
            $id_address = $db_address->id;
        }

        $dataEmployee = [
            'nip' => $kode,
            'pin' => $pin,
            'pin' => $pin,
            'nik' => $nik,
            'no_kk' => $no_kk,
            'fullname' => $fullname,
            'alias_name' => $alias_name,
            'gender' => $gender,
            'mariage_status' => $mariage_status,
            'family_depents' => $family_depents,
            'employee_type' => $employee_type,
            'address_id' => $id_address,
            'position_id' => $position,
            'entry_date' => $entry_date,
            'salary_id' => $id_salary,
            'premi' => $premi,
            'location' => $location,
            'payment_type' => $payment_type,
            'jkn_number' => $jkn_number,
            'jkp_number' => $jkp_number,
            'status' => $status,
        ];

        $bank_id = 0;
        if($payment_type == "ATM"){
            $db_bank = Bank::where('id', $number_account)->orWhere('number_account', $number_account)->first();
            if($db_bank == null){
                $bankData = [
                    "bank_name" => $bank_name,
                    "bank_account" => $bank_account,
                    "number_account" => $number_account,
                ];
                $bank_id = Bank::create($bankData);
            }else{
                $bank_id = $db_bank->id;
            }
            $dataEmployee['bank_id'] = $bank_id;
        }
        Employee::create($dataEmployee);
        return redirect()->route('karyawan.index')->with('status', 'saved');
    }

    public function edit($id){
        $employee = Employee::with(['salary', 'address'])->findOrFail($id);
        $genders = [
                [
                    "value" => 1,
                    "name" => "L"
                ],
                [
                    "value" => 0,
                    "name" => "P"
                ]
        ];

        $statuses = [
                [
                    "value" => 1,
                    "name" => "Aktif"
                ],
                [
                    "value" => 0,
                    "name" => "Nonaktif"
                ]
        ];

        $mariage_statuses = [
            'Belum kawin',
            'Kawin belum tercatat', 
            'Kawin tercatat', 
            'Cerai hidup', 
            'Cerai mati'
        ];

        $locations = [
            'Bukir Utara',
            'Bukir Selatan',
            'Kaligung'
        ];

        $employee_types = [
            'Harian',
            'Bulanan',
            'Borongan'
        ];

        $payments = [
            'ATM',
            'Tunai',
        ];

        $addresses = Address::orderBy('id', 'desc')->get();
        $banks = Bank::orderBy('id', 'desc')->get();
        $positions = Position::where("type", "Bagian")->with(['parent.parent'])->get()->map(function($position){
            return [
                'id' => $position->id,
                'name' => $position->name,
                'grandparent' => optional(optional($position->parent)->parent)->name, // Pastikan tidak error jika null
            ];
        });

        return view('pages.employees.edit', compact([
            'employee',
            'genders',
            'addresses', 
            'positions', 
            'locations',
            'payments',
            'mariage_statuses',
            'statuses',
            'employee_types',
            'banks'
        ]));
    }
    public function update(Request $request, $id){
        $employee = Employee::with(['salary', 'address', 'bank'])->findOrFail($id);
        
        // membuat validasi karyawan
        $request->validate([
            'nik' => 'required|numeric',
            'no_kk' => 'required|numeric',
            'fullname' => 'required',
            'alias_name' => 'required',
            'gender' => 'required|in:0,1',
            'mariage_status' => 'required|in:Belum kawin,Kawin belum tercatat,Kawin tercatat,Cerai hidup,Cerai mati',
            'family_depents' => 'required|in:0,1,2,3|numeric',
            'employee_type' => 'required|in:Harian,Bulanan',
            'position' => 'required|exists:positions,id',
            'entry_date' => 'required|date',
            'salary' => 'required|numeric',
            'location' => 'required|in:Bukir Utara,Bukir Selatan,Kaligung',
            'payment_type' => 'required|in:ATM,Tunai',
        ]);

        // inisialisasi
        $pin = $request->pin;
        $nik = $request->nik;
        $no_kk = $request->no_kk;
        $fullname = $request->fullname;
        $alias_name = $request->alias_name;
        $gender = $request->gender;
        $mariage_status = $request->mariage_status;
        $family_depents = $request->family_depents;
        $employee_type = $request->employee_type;
        $address = $request->address;
        $rt = $request->rt;
        $rw = $request->rw;
        $kelurahan = $request->kelurahan;
        $kecamatan = $request->kecamatan;
        $city = $request->city;
        $position = $request->position;
        $entry_date = $request->entry_date;
        $salary = $request->salary;
        $premi = $request->premi;
        $location = $request->location;
        $payment_type = $request->payment_type;
        $bank_name = $request->bank_name;
        $bank_account = $request->bank_account;
        $number_account = $request->number_account;
        $jkn_number = $request->jkn_number;
        $jkp_number = $request->jkp_number;
        $jkp_number = $request->jkp_number;
        $status = $request->status;

        // validasi tambahan
        if($premi != null){
            $request->validate([
                "premi" => 'required'
            ]);
        }
        if($jkn_number != null){
            $request->validate([
                "jkn_number" => 'required'
            ]);
        }
        if($jkp_number != null){
            $request->validate([
                "jkp_number" => 'required'
            ]);
        }
        
        if($entry_date != $employee->entry_date){
            // buat nip karyawan berdasarkan tanggal masuk
            // kode nip
            $kode = "";
            $max_code = DB::select("SELECT max(right(nip, 2)) as kode FROM employees WHERE entry_date LIKE '$entry_date%'");
            if (count($max_code) > 0) {
                foreach ($max_code as $q) {
                    $no = ((int)$q->kode) + 1;
                    $kd = sprintf("%02s", $no);
                }
            } else {
                $kd = "01";
            }
            $tanggal_masuk_code = str_replace('-', '', $entry_date);
            $kode = $tanggal_masuk_code . $kd;
        }else{
            $kode = $employee->nip;
        }

        // cek gaji apakah di table gaji sudah ada kalau tidak ada bikin baru
        $id_salary = 0;
        $db_salary = Salary::where('salary', $salary)->first();
        if($db_salary == null){
            $id_salary = Salary::create(['salary' =>$salary]);
        }else{
            $id_salary = $db_salary->id;
        }

        $id_address = 0;
        // ambil data dari db berdasarkan alamat yg di input
        $db_address = Address::where('id', $address)->orWhere('address', $address)->where('rt', $rt)->where('rw', $rw)->where('city', $city)->first();
        // cek alamat apakah alamat sudah ada kalau tidak ada bikin baru
        if($db_address == null){
            if($address == null || $rt == null|| $rw == null|| $kelurahan == null|| $kecamatan == null || $city == null){
                return redirect()->back()->with('status', 'addressErr');
            }
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

            $employee->nip = $kode;
            $employee->pin = $pin;
            $employee->pin = $pin;
            $employee->nik = $nik;
            $employee->no_kk = $no_kk;
            $employee->fullname = $fullname;
            $employee->alias_name = $alias_name;
            $employee->gender = $gender;
            $employee->mariage_status = $mariage_status;
            $employee->family_depents = $family_depents;
            $employee->employee_type = $employee_type;
            $employee->address_id = $id_address->id;
            $employee->position_id = $position;
            $employee->entry_date = $entry_date;
            $employee->salary_id = $id_salary;
            $employee->premi = $premi;
            $employee->location = $location;
            $employee->payment_type = $payment_type;
            $employee->jkn_number = $jkn_number;
            $employee->jkp_number = $jkp_number;
            $employee->status = $status;

            $bank_id = 0;
            if($payment_type == "ATM"){
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
                $employee->bank_id = $bank_id->id;
            }
        $employee->save();
        return redirect()->route('karyawan.index')->with('status', 'edited');
    }

    public function destroy($id){
        Employee::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'deleted');
    }

    public function importEmployees(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $import = new EmployeeImport();

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

    public function getAddress(Request $request){
        $typeValue = $request->type;

        $response = Address::where("id", $request->id)->first();
        return response()->json($response);
    }
}
