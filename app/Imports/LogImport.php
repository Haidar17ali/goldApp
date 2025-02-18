<?php

namespace App\Imports;

use App\Models\Log;use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;

class LogImport implements ToModel, WithHeadingRow, WithBatchInserts, SkipsOnFailure
{
    use SkipsFailures;

    private $errors = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $type;

    // Constructor untuk menerima data dari controller
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function model(array $row)
    {
        // inisialisasi
        $id_produksi = $row['id_produksi'];
        $barcode = $row['barcode'];
        $quality = $row['kualitas'];
        $length = $row['panjang'];
        $diameter = $row['diameter'];
        $quantity = $row['jumlah'];
        $type = $this->type;
        
        $code = 0;
        if($type == 'Sengon'){
            $code = 'SGN.'. substr($quality, 0,2).'.'.$length.'.'.$diameter;
        }elseif($type == 'Merbau'){
            $code = 'MBU.'. substr($quality, 0,2).'.'.$length.'.'.$diameter;
        }  
        
        $db_code = Log::where('code', $code)->where('type', $type)->first();
        
        if($db_code){
            $db_code->quantity += $quantity;
            $db_code->save();
        }else{
            $data = [
                'id_produksi' => $id_produksi,
                'barcode' => $barcode,
                'code' => $code,
                'type' => $type,
                'quality' => $quality,
                'length' => $length,
                'diameter' => $diameter,
                'quantity' => $quantity,
            ];
            
            Log::create($data);
        }
        
    }

    public function rules(): array
    {
        if ($this->type == "Merbau") {
            return [
                'id_produksi' => 'required',
                'barcode' => 'required',
                'quality' => 'required|in:Super,Afkir',
                'length' => 'required|decimal:0,4',
                'diameter' => 'required|decimal:0,4',
                'quantity' => 'required|decimal:0,4',
            ];
        }else{
            return [
                'quality' => 'required|in:Super,Afkir',
                'length' => 'required|decimal:0,4',
                'diameter' => 'required|decimal:0,4',
                'quantity' => 'required|decimal:0,4',
            ];
        }
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
