<?php

namespace App\Helpers;

use App\Models\CustomerSupplier;
use App\Models\Journal;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionMarketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnlineSaleHelper
{

    public static function store(
        array $validated,
        Request $request,
        int $marketplaceId
    ): Transaction {

        return DB::transaction(function () use (
            $validated,
            $request,
            $marketplaceId
        ) {

            /*
        |--------------------------------------------------------------------------
        | Upload Foto
        |--------------------------------------------------------------------------
        */

            $photo = self::uploadPhoto(
                $request->photo_base64
            );

            /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        */

            $customer = self::saveCustomer(
                null,
                $validated
            );

            /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

            $user = Auth::user();

            if (!$user->profile || !$user->profile->branch_id) {
                throw new \Exception('User belum memiliki cabang');
            }

            $branchId = $user->profile->branch_id;

            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */

            $totalData = self::calculateTotal($validated);

            /*
            |--------------------------------------------------------------------------
            | Header Transaction
            |--------------------------------------------------------------------------
            */

            $transaction = Transaction::create([

                'type' => 'penjualan',

                'purchase_type' => 'new',

                'branch_id' => $branchId,

                'storage_location_id' => 1,

                'transaction_date' => now(),

                'invoice_number' => $validated['invoice_number'],

                'customer_id' => $customer?->id,

                'note' => $validated['note'],

                'photo' => $photo,

                'total' => $totalData['total'],

                'manik_price' => $totalData['manik_price'],

                'created_by' => Auth::id(),

            ]);

            /*
        |--------------------------------------------------------------------------
        | Marketplace
        |--------------------------------------------------------------------------
        */

            self::saveMarketplace(

                transaction: $transaction,

                marketplaceId: $marketplaceId,

                marketplaceTotal: $validated['marketplace_total'],

                receivedAmount: $validated['received_amount']

            );

            /*
        |--------------------------------------------------------------------------
        | Detail
        |--------------------------------------------------------------------------
        */

            $totalHpp = self::saveDetails(

                transaction: $transaction,

                details: $validated['details'],

                branchId: $branchId

            );

            /*
        |--------------------------------------------------------------------------
        | Jurnal
        |--------------------------------------------------------------------------
        */

            self::postJournal(

                transaction: $transaction,

                marketplaceId: $marketplaceId,

                totalHpp: $totalHpp

            );

            return $transaction;
        });
    }

    public static function update(
        int $transactionId,
        array $validated,
        Request $request,
        int $marketplaceId
    ): Transaction {

        return DB::transaction(function () use (
            $transactionId,
            $validated,
            $request,
            $marketplaceId
        ) {

            /*
        |--------------------------------------------------------------------------
        | Ambil Transaction
        |--------------------------------------------------------------------------
        */

            $transaction = Transaction::with([
                'details.productVariant',
                'transactionMarketplace',
                'customer',
            ])->findOrFail($transactionId);

            /*
        |--------------------------------------------------------------------------
        | Rollback
        |--------------------------------------------------------------------------
        */

            self::rollback($transaction);

            /*
        |--------------------------------------------------------------------------
        | Upload Foto
        |--------------------------------------------------------------------------
        */

            $photo = self::uploadPhoto(
                $request->photo_base64,
                $transaction->photo
            );

            /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        */

            $customer = self::saveCustomer(
                $transaction,
                $validated
            );

            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */

            $totalData = self::calculateTotal($validated);

            /*
        |--------------------------------------------------------------------------
        | Update Header
        |--------------------------------------------------------------------------
        */

            $transaction->update([

                'invoice_number' => $validated['invoice_number'],

                'customer_id' => $customer?->id,

                'note' => $validated['note'],

                'photo' => $photo,

                'total' => $totalData['total'],

                'manik_price' => $totalData['manik_price'],

            ]);

            /*
        |--------------------------------------------------------------------------
        | Update Marketplace
        |--------------------------------------------------------------------------
        */

            self::saveMarketplace(

                transaction: $transaction,

                marketplaceId: $marketplaceId,

                marketplaceTotal: $validated['marketplace_total'],

                receivedAmount: $validated['received_amount']

            );

            /*
        |--------------------------------------------------------------------------
        | Simpan Detail Baru
        |--------------------------------------------------------------------------
        */

            $totalHpp = self::saveDetails(

                transaction: $transaction,

                details: $validated['details'],

                branchId: $transaction->branch_id

            );

            /*
        |--------------------------------------------------------------------------
        | Posting Jurnal Baru
        |--------------------------------------------------------------------------
        */

            self::postJournal(

                transaction: $transaction,

                marketplaceId: $marketplaceId,

                totalHpp: $totalHpp

            );

            return $transaction;
        });
    }

    public static function destroy(int $transactionId): void
    {
        DB::transaction(function () use ($transactionId) {

            $transaction = Transaction::with([
                'details.productVariant',
                'transactionMarketplace',
            ])->findOrFail($transactionId);

            /*
        |--------------------------------------------------------------------------
        | Rollback
        |--------------------------------------------------------------------------
        */

            self::rollback($transaction);

            /*
        |--------------------------------------------------------------------------
        | Hapus Marketplace
        |--------------------------------------------------------------------------
        */

            $transaction->transactionMarketplace()?->delete();

            /*
        |--------------------------------------------------------------------------
        | Hapus Foto
        |--------------------------------------------------------------------------
        */

            if (
                $transaction->photo &&
                file_exists(public_path($transaction->photo))
            ) {
                @unlink(public_path($transaction->photo));
            }

            /*
        |--------------------------------------------------------------------------
        | Hapus Header
        |--------------------------------------------------------------------------
        */

            $transaction->delete();
        });
    }

    public static function calculateTotal(array $validated): array
    {
        $manikPrice = (float) ($validated['manik_price'] ?? 0);

        $productTotal = collect($validated['details'])
            ->sum(function ($item) {
                return (float) $item['harga_jual'];
            });

        $grandTotal = $productTotal + $manikPrice;

        return [

            // Total seluruh harga produk
            'product_total' => $productTotal,

            // Harga manik
            'manik_price' => $manikPrice,

            // Grand Total Nota
            'total' => $grandTotal,

        ];
    }

    public static function uploadPhoto(?string $photoBase64, ?string $oldPhoto = null): ?string
    {
        if (!$photoBase64) {
            return $oldPhoto;
        }

        @list(, $fileData) = explode(',', $photoBase64);

        if (!$fileData) {
            return $oldPhoto;
        }

        if (
            $oldPhoto &&
            file_exists(public_path($oldPhoto))
        ) {
            @unlink(public_path($oldPhoto));
        }

        $fileName = 'sales_' . time() . '.png';

        $path = public_path('assets/images/penjualan');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents(
            $path . '/' . $fileName,
            base64_decode($fileData)
        );

        return 'assets/images/penjualan/' . $fileName;
    }

    public static function saveCustomer(
        ?Transaction $transaction,
        array $validated
    ): ?CustomerSupplier {

        if (empty($validated['customer_name'])) {
            return null;
        }

        if ($transaction && $transaction->customer) {

            $transaction->customer->update([

                'name' => trim($validated['customer_name']),

                'phone_number' => $validated['customer_phone'],

                'address' => $validated['customer_address'],

            ]);

            return $transaction->customer;
        }

        return CustomerSupplier::firstOrCreate(

            [
                'name' => trim($validated['customer_name'])
            ],

            [
                'phone_number' => $validated['customer_phone'],

                'address' => $validated['customer_address'],

                'type' => 'customer'
            ]

        );
    }

    public static function rollback(Transaction $transaction): void
    {
        $journal = Journal::with('items')

            ->where('source_type', 'sale_online')

            ->where('source_id', $transaction->id)

            ->where('is_reversal', false)

            ->latest()

            ->first();

        if ($journal) {

            AccountingHelper::reverse(
                $journal,
                'Edit Penjualan Online'
            );
        }

        self::restoreStock($transaction);

        $transaction->details()->delete();
    }

    public function saveTransactionDetails(
        Transaction $transaction,
        array $details,
        int $branchId
    ): float {

        $totalHpp = 0;

        foreach ($details as $detail) {

            $variant = ProductVariant::with([
                'product',
                'karat'
            ])->findOrFail($detail['variant_id']);

            $hargaEmas = GoldHelper::getHargaByKarat(
                $variant->karat_id
            );

            $gram = $variant->gram ?? 0;

            $hpp = self::calculateHpp($variant);

            $totalHpp += $hpp;

            TransactionDetail::create([

                'transaction_id' => $transaction->id,

                'product_variant_id' => $variant->id,

                'unit_price' => $detail['harga_jual'],

                'type' => $variant->type,

                'note' => null,

            ]);

            StockHelper::stockOut(

                variantId: $variant->id,

                branchId: $branchId,

                storageLocationId: $transaction->storage_location_id,

                quantity: 1,

                weight: $gram,

                refType: 'Transaction',

                refId: $transaction->id,

                note: 'Penjualan Online',

                userId: auth()->id(),

                goldType: $variant->type

            );
        }

        return $totalHpp;
    }

    public static function postJournal(
        Transaction $transaction,
        int $marketplaceId,
        float $totalHpp
    ): void {
        $coa = self::getCoaMapping($marketplaceId);


        AccountingHelper::post([

            'date' => $transaction->transaction_date,

            'reference' => $transaction->invoice_number,

            'description' => 'Penjualan Online ' . $transaction->invoice_number,

            'source_type' => 'sale_online',

            'source_id' => $transaction->id,

            'lines' => [

                [
                    'account' => $coa['receivable'],
                    'debit' => $transaction->total + ($transaction->transactionMarketplace->received_amount - $transaction->total),
                ],

                [
                    'account' => $coa['hpp'],
                    'debit' => $totalHpp,
                ],

                [
                    'account' => $coa['sales'],
                    'credit' => $transaction->total,
                ],
                [
                    'account' => $coa['kas_kembalian_online'],
                    'credit' => $transaction->transactionMarketplace->received_amount - $transaction->total,
                ],

                [
                    'account' => $coa['stock'],
                    'credit' => $totalHpp,
                ],

            ]

        ]);
    }

    public static function restoreStock(Transaction $transaction): void
    {
        foreach ($transaction->details as $detail) {

            $variant = $detail->productVariant;

            StockHelper::stockIn(

                variantId: $variant->id,

                branchId: $transaction->branch_id,

                storageLocationId: $transaction->storage_location_id,

                quantity: 1,

                weight: $variant->gram,

                refType: 'Transaction',

                refId: $transaction->id,

                note: 'Rollback Penjualan Online',

                userId: auth()->id(),

                goldType: $variant->type

            );
        }
    }

    public static function calculateHpp(ProductVariant $variant): float
    {
        $hargaEmas = GoldHelper::getHargaByKarat(
            $variant->karat_id
        );

        return $hargaEmas * ($variant->gram ?? 0);
    }

    public static function saveMarketplace(
        Transaction $transaction,
        int $marketplaceId,
        float $marketplaceTotal,
        float $receivedAmount
    ): void {
        TransactionMarketplace::updateOrCreate(

            [
                'transaction_id' => $transaction->id
            ],

            [
                'marketplace_id' => $marketplaceId,

                'marketplace_total' => $marketplaceTotal,

                'received_amount' => $receivedAmount,
                'payment_status' => "Pending",

            ]

        );
    }

    public static function getCoaMapping(int $marketplaceId): array
    {
        return [

            'receivable' => match ($marketplaceId) {

                1 => '102.00.01',

                2 => '102.00.02',

                3 => '102.00.03',

                default => throw new \Exception('COA Piutang belum dibuat.')
            },

            'sales' => match ($marketplaceId) {

                1 => '501.03.001',

                2 => '501.03.002',

                3 => '501.03.003',

                default => throw new \Exception('COA Penjualan belum dibuat.')
            },

            'hpp' => '502.01.04',

            'stock' => '103.03.01',
            'kas_kembalian_online' => '101.00.11',

        ];
    }

    public static function saveDetails(
        Transaction $transaction,
        array $details,
        int $branchId
    ): float {

        $totalHpp = 0;

        foreach ($details as $detail) {

            $variant = ProductVariant::with([
                'product',
                'karat'
            ])->findOrFail($detail['variant_id']);

            /*
        |--------------------------------------------------------------------------
        | Hitung HPP
        |--------------------------------------------------------------------------
        */

            $hpp = self::calculateHpp($variant);

            $totalHpp += $hpp;

            /*
        |--------------------------------------------------------------------------
        | Simpan Detail
        |--------------------------------------------------------------------------
        */

            TransactionDetail::create([

                'transaction_id'     => $transaction->id,

                'product_variant_id' => $variant->id,

                'unit_price'         => $detail['harga_jual'],

                'type'               => $variant->type,

                'note'               => $detail['note'] ?? null,

            ]);

            /*
        |--------------------------------------------------------------------------
        | Kurangi Stock
        |--------------------------------------------------------------------------
        */

            StockHelper::stockOut(

                variantId: $variant->id,

                branchId: $branchId,

                storageLocationId: $transaction->storage_location_id,

                quantity: 1,

                weight: $variant->gram,

                refType: 'Transaction',

                refId: $transaction->id,

                note: 'Penjualan Online',

                userId: Auth::id(),

                goldType: $variant->type

            );
        }

        return $totalHpp;
    }
}
