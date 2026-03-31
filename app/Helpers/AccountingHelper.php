<?php

namespace App\Helpers;

use App\Models\Journal;
use App\Models\JournalItem;
use App\Services\AccountingService;

class AccountingHelper
{
    public static function post($data)
    {
        return AccountingService::post($data);
    }

    public static function reverse($journal, $note = 'Reversal')
    {
        $newJournal = Journal::create([
            'date' => now(),
            'reference' => $journal->reference,
            'description' => $note . ': ' . $journal->description,
            'source_type' => $journal->source_type,
            'source_id' => $journal->source_id,
            'reversal_of' => $journal->id,
            'is_reversal' => true
        ]);

        foreach ($journal->items as $item) {

            JournalItem::create([
                'journal_id' => $newJournal->id,
                'chart_of_account_id' => $item->chart_of_account_id,
                'debit' => $item->credit,
                'credit' => $item->debit
            ]);
        }
    }
}
