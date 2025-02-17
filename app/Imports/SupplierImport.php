<?php

namespace App\Imports;

use App\Models\Supplier;
use App\Models\Salary;
use App\Models\Address;
use App\Models\Bank;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;

class SupplierImport implements ToModel, WithHeadingRow, WithBatchInserts, SkipsOnFailure
{
    use SkipsFailures;

    private $errors = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // inisialisasi
        $nik = $row['nik'];
        $type = $row['type'];
        $name = $row['name'];
        $phone = $row['no_hp'];
        $address = $row['alamat'];
        $rt = $row['rt'];
        $rw = $row['rw'];
        $kelurahan = $row['kelurahan'];
        $kecamatan = $row['kecamatan'];
        $city = $row['kota'];
        $bank_name = $row['bank'];
        $bank_account = $row['nama_rek'];
        $number_account = $row['no_rek'];

        $addressDb = null;
        if($address != null){
            // Ambil atau buat alamat baru
            $addressData = [
                'address' => $address,
                'rt' => $rt,
                'rw' => $rw,
                'kelurahan' => $kelurahan,
                'kecamatan' => $kecamatan,
                'city' => $city,
            ];
            $addressDb = Address::where($addressData)->first();
            if (!$addressDb) {
                $addressDb = Address::create($addressData);
            }
        }

        $bank = null;
        if($number_account != null){
            // Ambil atau buat bank baru
            $bankData = [
                'bank_name' => $bank_name,
                'bank_account' => $bank_account,
                'number_account' => $number_account,
            ];
            $bank = Bank::where($bankData)->first();
            if (!$bank) {
                $bank = Bank::create($bankData);
            }
        }

        // Data karyawan
        $dataSupplier = [
            'nik' => $nik,
            'supplier_type' => $type,
            'name' => $name,
            'phone' => $phone,
        ];

        if($address != null){
            $dataSupplier['address_id'] = $addressDb->id;
        }
        if($number_account != null){
            $dataSupplier['bank_id'] = $bank->id;
        }
        
        return new Supplier($dataSupplier);
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:Sengon,Merbau,Pembantu',
            'name' => 'required',
            'phone' => 'numeric',
        ];
    }

    public function batchSize(): int
    {
        return 100; // Jumlah baris yang diproses dalam satu batch
    }

    public function onFailure(Failure ...$failures)
    {
        // Menyimpan pesan error untuk ditampilkan
        foreach ($failures as $failure) {
            $this->errors[] = "Baris " . $failure->row() . ": " . implode(", ", $failure->errors());
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
