<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class AllLPBSupplierReportExport implements FromView, WithEvents
{
    protected $groupedLpbs;
    protected $grandTotal;
    protected $periode;
    protected $headerDate;
    protected $startDate;
    protected $endDate;
    protected $dateBy;

    public function __construct($groupedLpbs, $grandTotal, $periode, $headerDate, $startDate, $endDate, $dateBy)
    {
        $this->groupedLpbs = $groupedLpbs;
        $this->grandTotal = $grandTotal;
        $this->periode = $periode;
        $this->headerDate = $headerDate;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->dateBy = $dateBy;
    }

    public function view(): View
    {
        return view('pages.Export.all-lpb-supplier-report', [
            'groupedLpbs' => $this->groupedLpbs,
            'grandTotal' => $this->grandTotal,
            'periode' => $this->periode,
            'headerDate' => $this->headerDate,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'dateBy' => $this->dateBy,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge judul dan styling baris 1
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Styling header kolom (baris ke-2)
                $sheet->getStyle('A2:Z2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE0E0E0'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF999999'],
                        ],
                    ],
                ]);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $dataRange = "A3:{$highestColumn}{$highestRow}";

                // Styling semua data
                $sheet->getStyle($dataRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC'],
                        ],
                    ],
                ]);

                // Warnai semua isi kolom H (PPh) dengan warna merah
                for ($row = 3; $row <= $highestRow; $row++) {
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'font' => ['color' => ['argb' => Color::COLOR_RED]],
                    ]);
                }
            },
        ];
    }

}
