<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Karat;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StockHelper
{
    /**
     * Pastikan product, karat, dan variant ada — buat otomatis jika belum ada
     */
    public static function ensureVariant($productName, $karatName = null, $gram = null)
    {
        // Pastikan produk ada
        $product = Product::firstOrCreate(
            ['name' => $productName],
            ['code' => strtoupper(Str::slug($productName))]
        );

        // Pastikan karat ada
        $karat = null;
        if ($karatName) {
            $karat = Karat::firstOrCreate(
                ['name' => strtoupper($karatName)],
                ['description' => 'Auto-generated']
            );
        }

        // Pastikan variant ada
        return ProductVariant::firstOrCreate(
            [
                'product_id' => $product->id,
                'karat_id' => $karat ? $karat->id : null,
                'gram' => $gram,
            ],
            function () use ($product, $karat, $gram) {
                $karatCode = $karat ? $karat->name : 'NOKRT';
                return [
                    'sku' => strtoupper($product->name . '-' . $karatCode . '-' . ($gram ?? 'GEN')),
                    'barcode' => strtoupper(Str::random(12)),
                    'default_price' => 0,
                ];
            }
        );
    }

    /**
     * Catat pergerakan stok
     */
    public static function moveStock($variantId, $branchId, $storageLocationId, $type, $quantity, $weight = null, $referenceType = null, $referenceId = null, $note = null, $userId = null)
    {
        return DB::transaction(function () use ($variantId, $branchId, $storageLocationId, $type, $quantity, $weight, $referenceType, $referenceId, $note, $userId) {

            $movement = StockMovement::create([
                'product_variant_id' => $variantId,
                'branch_id' => $branchId,
                'storage_location_id' => $storageLocationId,
                'type' => $type,
                'quantity' => $quantity,
                'weight' => $weight,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'created_by' => $userId,
            ]);

            // Update stok utama
            $stock = Stock::firstOrCreate([
                'product_variant_id' => $variantId,
                'branch_id' => $branchId,
                'storage_location_id' => $storageLocationId,
            ], [
                'quantity' => 0,
                'weight' => 0,
            ]);

            if (in_array($type, ['in', 'loan_in'])) {
                $stock->quantity += $quantity;
                $stock->weight += $weight ?? 0;
            } elseif (in_array($type, ['out', 'loan_out'])) {
                $stock->quantity -= $quantity;
                $stock->weight -= $weight ?? 0;
            } elseif ($type === 'adjustment') {
                $stock->quantity = $quantity;
                $stock->weight = $weight ?? $stock->weight;
            }

            $stock->save();

            return $movement;
        });
    }

    /**
     * Shortcut untuk stok masuk
     */
    public static function stockIn($variantId, $branchId, $storageLocationId, $quantity, $weight = null, $refType = null, $refId = null, $note = null, $userId = null)
    {
        return self::moveStock($variantId, $branchId, $storageLocationId, 'in', $quantity, $weight, $refType, $refId, $note, $userId);
    }

    /**
     * Shortcut untuk stok keluar
     */
    public static function stockOut($variantId, $branchId, $storageLocationId, $quantity, $weight = null, $refType = null, $refId = null, $note = null, $userId = null)
    {
        return self::moveStock($variantId, $branchId, $storageLocationId, 'out', $quantity, $weight, $refType, $refId, $note, $userId);
    }

    /**
     * Peminjaman emas oleh influencer
     */
    public static function loanOut($variantId, $branchId, $storageLocationId, $quantity, $weight = null, $refType = 'Loan', $refId = null, $note = 'Dipinjam influencer', $userId = null)
    {
        return self::moveStock($variantId, $branchId, $storageLocationId, 'loan_out', $quantity, $weight, $refType, $refId, $note, $userId);
    }

    /**
     * Pengembalian emas oleh influencer
     */
    public static function loanIn($variantId, $branchId, $storageLocationId, $quantity, $weight = null, $refType = 'Loan', $refId = null, $note = 'Dikembalikan influencer', $userId = null)
    {
        return self::moveStock($variantId, $branchId, $storageLocationId, 'loan_in', $quantity, $weight, $refType, $refId, $note, $userId);
    }
}
