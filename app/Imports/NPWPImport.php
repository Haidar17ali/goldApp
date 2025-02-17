<?php

namespace App\Imports;

use App\Models\npwp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;

class NPWPImport implements ToModel, WithHeadingRow, WithBatchInserts, SkipsOnFailure
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
        $npwp = $row['npwp'];
        $nitku = $row['nitku'];
        $name = $row['name'];

       

        // Data karyawan
        $dataNPWP = [
            'npwp' => $nitku,
            'nitku' => $nitku,
            'name' => $name,
        ];
        
        return new npwp($dataNPWP);
    }

    public function rules(): array
    {
        return [
            'npwp' => 'required',
            'name' => 'required',
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
