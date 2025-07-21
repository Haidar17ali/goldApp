<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LpbExportByNpwp implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'NPWP',
            'Nama',
            'Total QTY',
            'Total Kubikasi',
            'Nilai LPB',
            'Konversi/Borongan',
            'Nilai',
            'PPH22',
            'Grand Total',
        ];
    }

}
