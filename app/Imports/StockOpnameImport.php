<?php

namespace App\Imports;

use App\Models\{
    Karat,
    Product,
    ProductVariant,
    Stock,
    StockAdjustment,
    StockAdjustmentDetail
};
use App\Helpers\StockHelper;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOpnameImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            $adjustment = StockAdjustment::create([
                'branch_id' => auth()->user()->branch_id ?? 1,
                'storage_location_id' => 1,
                'adjustment_date' => now(),
                'note' => 'Import Stock Opname',
                'created_by' => auth()->id(),
            ]);

            foreach ($rows as $row) {

                if (empty($row['product']) || empty($row['actual_qty'])) {
                    continue;
                }

                $productName = trim($row['product']);
                $karatName   = trim($row['karat'] ?? null);
                $type        = strtolower(trim($row['type'])); // gold_type
                $actualQty   = (int) $row['actual_qty'];
                $weight      = $row['weight'] !== null ? (float) $row['weight'] : null;

                if ($actualQty < 0) continue;

                /* ===============================
                 * 1️⃣ Pastikan VARIANT ADA
                 * =============================== */
                $variant = StockHelper::ensureVariant(
                    $productName,
                    $karatName,
                    $weight,
                    $type
                );

                /* ===============================
                 * 2️⃣ Ambil stok sistem
                 * =============================== */
                $stock = Stock::where([
                    'product_variant_id' => $variant->id,
                    'branch_id' => $adjustment->branch_id,
                    'storage_location_id' => $adjustment->storage_location_id,
                    'type' => $type,
                ])->first();

                $systemQty = $stock?->quantity ?? 0;
                $difference = $actualQty - $systemQty;

                /* ===============================
                 * 3️⃣ Simpan detail opname
                 * =============================== */
                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_variant_id' => $variant->id,
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'type' => $type,
                ]);

                /* ===============================
                 * 4️⃣ CATAT KE STOCK MOVEMENT
                 * =============================== */
                StockHelper::moveStock(
                    product_variant_id: $variant->id,
                    branchId: 1,
                    storageLocationId: 1,
                    type: 'adjustment',
                    quantity: $actualQty,
                    weight: $weight,
                    referenceType: StockAdjustment::class,
                    referenceId: $adjustment->id,
                    note: 'Stock Opname Import',
                    userId: auth()->id(),
                    goldType: $type
                );
            }
        });
    }
}
