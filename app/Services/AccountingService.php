<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalItem;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public static function post($data)
    {
        return DB::transaction(function () use ($data) {

            $journal = Journal::create([
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($data['lines'] as $line) {

                $account = ChartOfAccount::where('code', $line['account'])->firstOrFail();

                $debit = $line['debit'] ?? 0;
                $credit = $line['credit'] ?? 0;

                $totalDebit += $debit;
                $totalCredit += $credit;

                JournalItem::create([
                    'journal_id' => $journal->id,
                    'chart_of_account_id' => $account->id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $line['description'] ?? null
                ]);
            }

            if ($totalDebit != $totalCredit) {
                throw new \Exception("Journal not balanced");
            }

            return $journal;
        });
    }
}
