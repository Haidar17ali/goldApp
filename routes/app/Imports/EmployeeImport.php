<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Address;
use App\Models\Bank;
use App\Models\Position;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeImport implements ToModel , WithHeadingRow, WithBatchInserts, SkipsOnFailure
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
        if (empty($row['fullname']) || empty($row['entry_date'])) {
            return null;
        }
        // ubah Format tanggal
        $entry_date= date('Y-m-d', ($row['entry_date'] - 25569) * 86400);
        // Buat atau ambil data gaji
        $salary = Salary::firstOrCreate(['salary' => $row['salary']]);

        $address = null;
        if ($row["address"] != null) {
            // Ambil atau buat alamat baru
            $addressData = [
                'address' => $row['address'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
                'kelurahan' => $row['kelurahan'],
                'kecamatan' => $row['kecamatan'],
                'city' => $row['city'],
            ];
            $address = Address::where($addressData)->first();
            if (!$address) {
                $address = Address::create($addressData);
            }
        }

        $bank = null;
        if ($row["bank_account"] != null) {
            // Ambil atau buat bank baru
            $bankData = [
                'bank_name' => $row['bank_name'],
                'bank_account' => $row['bank_account'],
                'number_account' => $row['number_account'],
            ];
            $bank = Bank::where($bankData)->first();
            if (!$bank) {
                $bank = Bank::create($bankData);
            }
        }

        // Buat NIP
        $entryDate = str_replace('-', '', $entry_date);
        $maxCode = Employee::where('entry_date', 'like', $entry_date . '%')->max('nip');
        $kd = $maxCode ? sprintf("%02d", ((int)substr($maxCode, -2)) + 1) : '01';
        $nip = $entryDate . $kd;

        // Data karyawan
        $dataEmployee = [
            'nip' => $nip,
            'pin' => $row['pin'],
            'nik' => $row['nik'],
            'no_kk' => $row['no_kk'],
            'fullname' => $row['fullname'],
            'alias_name' => $row['alias_name'],
            'gender' => $row['gender'],
            'mariage_status' => $row['mariage_status'],
            'family_depents' => $row['family_depents'],
            'employee_type' => $row['employee_type'],
            'address_id' => $address->id??null,
            'position_id' => $row['position'],
            'entry_date' => $entry_date,
            'salary_id' => $salary->id,
            'premi' => $row['premi'],
            'location' => $row['location'],
            'payment_type' => $row['payment_type'],
            'jkn_number' => $row['jkn_number'],
            'jkp_number' => $row['jkp_number'],
            'status' => $row['status'],
        ];
        
        if($bank != null){
            if ($row['payment_type'] == "ATM") {
                $dataEmployee['bank_id'] = $bank->id;
            }
        }

        return new Employee($dataEmployee);
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|numeric',
            'no_kk' => 'numeric|nullable',
            'fullname' => 'required',
            'alias_name' => 'required',
            'gender' => ['required', Rule::in([0, 1])],
            'mariage_status' => ['required', Rule::in(['Belum kawin', 'Kawin belum tercatat', 'Kawin tercatat', 'Cerai hidup', 'Cerai mati'])],
            'family_depents' => ['required', Rule::in([0, 1, 2, 3]), 'numeric'],
            'employee_type' => ['required', Rule::in(['Harian', 'Bulanan'])],
            'position' => 'required|exists:positions,id',
            'entry_date' => 'required|date',
            'salary' => 'required|numeric',
            'location' => ['required', Rule::in(['Bukir Utara', 'Bukir Selatan', 'Kaligung'])],
            'payment_type' => ['required', Rule::in(['ATM', 'Tunai'])],
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
