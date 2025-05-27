<?php

namespace App\Imports;

use App\Models\LPB;
use App\Models\LPBDetail;
use App\Models\Supplier;
use App\Models\StockTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ToCollection;

class LPBImport implements WithMultipleSheets
{
    protected $lpbs = [];
    protected $details = [];

    public function sheets(): array
    {
        return [
            'LPB' => new class implements ToCollection {
                public $lpbs = [];

                public function collection(Collection $rows)
                {
                    foreach ($rows->skip(1) as $row) {
                        $this->lpbs[] = [
                            'no_kitir' => $row[0],
                            'arrival_date' => $row[1],
                            'grader_id' => $row[2],
                            'tally_id' => $row[3],
                            'supplier_id' => $row[4],
                            'npwp_id' => $row[5],
                            'nopol' => $row[6],
                            'po_id' => $row[7],
                            'road_permit_id' => $row[8],
                            'perhutani' => $row[9],
                            'conversion' => $row[10],
                        ];
                    }
                }
            },
            'LPB Details' => new class implements ToCollection {
                public $details = [];

                public function collection(Collection $rows)
                {
                    foreach ($rows->skip(1) as $row) {
                        $this->details[] = [
                            'no_kitir' => $row[0],
                            'product_code' => $row[1],
                            'length' => $row[2],
                            'diameter' => $row[3],
                            'qty' => $row[4],
                            'price' => $row[5],
                            'quality' => $row[6],
                        ];
                    }
                }
            }
        ];
    }

    public function importData($sheets)
    {
        DB::beginTransaction();
        try {
            $lpbs = $sheets['LPB']->lpbs;
            $details = $sheets['LPB Details']->details;

            foreach ($lpbs as $lpbData) {
                $lpbCode = generateCode('LPB', 'l_p_b_s', 'date');

                $supplier = Supplier::with('bank')->find($lpbData['supplier_id']);
                $bank_name = $supplier->bank->bank_name ?? 'tidak ditemukan';
                $bank_account = $supplier->bank->bank_account ?? '-';
                $number_account = $supplier->bank->number_account ?? '-';

                $lpb = LPB::create([
                    'code' => $lpbCode,
                    'date' => $lpbData['date'],
                    'arrival_date' => $lpbData['arrival_date'],
                    'po_id' => $lpbData['po_id'],
                    'no_kitir' => $lpbData['no_kitir'],
                    'grader_id' => $lpbData['grader_id'],
                    'tally_id' => $lpbData['tally_id'],
                    'road_permit_id' => $lpbData['road_permit_id'],
                    'supplier_id' => $lpbData['supplier_id'],
                    'npwp_id' => $lpbData['npwp_id'],
                    'bank_name' => $bank_name,
                    'bank_account' => $bank_account,
                    'number_account' => $number_account,
                    'nopol' => $lpbData['nopol'],
                    'conversion' => $lpbData['conversion'],
                    'perhutani' => $lpbData['perhutani'],
                    'status' => $lpbData['perhutani'] ? 'Terbayar' : 'Pending',
                    'created_by' => Auth::id(),
                ]);

                $lpbDetails = array_filter($details, fn($d) => $d['no_kitir'] == $lpbData['no_kitir']);
                foreach ($lpbDetails as $detail) {
                    LPBDetail::create([
                        'lpb_id' => $lpb->id,
                        'product_code' => $detail['product_code'],
                        'length' => $detail['length'],
                        'diameter' => $detail['diameter'],
                        'qty' => $detail['qty'],
                        'price' => $detail['price'],
                        'quality' => $detail['quality'],
                    ]);
                }

                StockTransaction::create([
                    'lpb_id' => $lpb->id,
                    'type' => 'Masuk',
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

