<?php

namespace App\Exports;

use App\Models\Journal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalReportExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $journals = Journal::with([
            'items.account'
        ])
            ->whereBetween('date', [
                $this->startDate,
                $this->endDate
            ])
            ->orderBy('date')
            ->get();

        $rows = collect();

        foreach ($journals as $journal) {

            foreach ($journal->items as $item) {

                $rows->push([
                    'tanggal' => $journal->date,
                    'referensi' => $journal->reference,
                    'deskripsi' => $journal->description,
                    'sumber' => strtoupper($journal->source_type),
                    'akun' => $item->account->name ?? '-',
                    'debit' => $item->debit,
                    'kredit' => $item->credit,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Referensi',
            'Deskripsi',
            'Sumber',
            'Nama Akun',
            'Debit',
            'Kredit',
        ];
    }
}
