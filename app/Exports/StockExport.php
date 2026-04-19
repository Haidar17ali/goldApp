<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromQuery, WithMapping, WithHeadings
{
    protected $type;

    // Kita terima parameter type di constructor
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function query()
    {
        // Gunakan eager loading (with) agar loading cepat dan tidak error
        $query = Stock::with(['productVariant.product', 'productVariant.karat']);

        // Jika type ditentukan, filter datanya
        if ($this->type) {
            $query->where('type', $this->type);
        }

        return $query;
    }

    // Header untuk kolom Excel
    public function headings(): array
    {
        return [
            'Nama Barang',
            'Kadar (Karat)',
            'Berat (Gram)',
            'Qty',
            'Tipe'
        ];
    }

    // Mapping data dari Model ke Kolom Excel
    public function map($stock): array
    {
        return [
            $stock->productVariant->product->name ?? '-', // Nama dari Product
            $stock->productVariant->karat->name ?? '-',   // Kadar dari Karat
            $stock->weight,                               // Berat
            $stock->quantity,                             // Qty
            $stock->type,                                 // Tipe Stock
        ];
    }
}