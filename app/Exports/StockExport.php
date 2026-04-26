<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromQuery, WithMapping, WithHeadings
{
    public function query()
    {
        return DB::table('stocks')
            ->join('product_variants', 'stocks.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('karats', 'product_variants.karat_id', '=', 'karats.id')
            ->select(
                'product_variants.barcode',
                'products.name as product_name',
                'karats.name as karat_name',
                'product_variants.gram',
                'stocks.type',

                DB::raw('SUM(COALESCE(stocks.quantity,0)) as total_qty')
            )
            ->groupBy(
                'product_variants.barcode',
                'products.name',
                'karats.name',
                'product_variants.gram',
                'stocks.type'
            )
            ->orderBy('products.name')
            ->orderBy('karats.name')
            ->orderBy('product_variants.gram')
            ->orderBy('stocks.type');
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Barang',
            'Kadar (Karat)',
            'Gram',
            'Tipe',
            'Qty'
        ];
    }

    public function map($row): array
    {
        return [
            $row->barcode,
            $row->product_name,
            $row->karat_name,
            $row->gram,
            $row->type,
            $row->total_qty,
        ];
    }
}
