<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LPBSupplierReportExport implements FromView, WithEvents
{
    protected $sortedResults, $pemilik, $periode, $nopolResult;
    protected $grandTotalQty, $grandTotalM3, $grandTotalNilai, $grandTotalPph;

    public function __construct($sortedResults, $pemilik, $periode, $nopolResult, $grandTotalQty, $grandTotalM3, $grandTotalNilai, $grandTotalPph)
    {
        $this->sortedResults = $sortedResults;
        $this->pemilik = $pemilik;
        $this->periode = $periode;
        $this->nopolResult = $nopolResult;
        $this->grandTotalQty = $grandTotalQty;
        $this->grandTotalM3 = $grandTotalM3;
        $this->grandTotalNilai = $grandTotalNilai;
        $this->grandTotalPph = $grandTotalPph;
    }

    public function view(): View
    {
        return view('pages.Export.lpb-supplier-report', [
            'sortedResults' => $this->sortedResults,
            'pemilik' => $this->pemilik,
            'periode' => $this->periode,
            'nopolResult' => $this->nopolResult,
            'grandTotalQty' => $this->grandTotalQty,
            'grandTotalM3' => $this->grandTotalM3,
            'grandTotalNilai' => $this->grandTotalNilai,
            'grandTotalPph' => $this->grandTotalPph
        ]);
    }

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class => function (AfterSheet $event) {
            //     $sheet = $event->sheet;

            //     // Bold untuk info di atas
            //     $sheet->getDelegate()->getStyle('A1:B3')->getFont()->setBold(true);

            //     // Bold untuk header tabel
            //     $sheet->getDelegate()->getStyle('A5:G5')->getFont()->setBold(true);

            //     $highestRow = $sheet->getDelegate()->getHighestRow();

            //     for ($row = 6; $row <= $highestRow; $row++) {
            //         $firstCell = $sheet->getDelegate()->getCell("A$row")->getValue();
            //         $secondCell = $sheet->getDelegate()->getCell("B$row")->getValue();

            //         if (is_string($firstCell) && stripos($firstCell, 'total') !== false) {
            //             // Warna untuk subtotal dan grand total
            //             $sheet->getDelegate()->getStyle("A$row:G$row")->getFont()->setBold(true);
            //             $sheet->getDelegate()->getStyle("A$row:G$row")->getFill()
            //                 ->setFillType('solid')
            //                 ->getStartColor()->setRGB('D3D3D3'); // abu terang
            //         } elseif ($secondCell === null || $secondCell === '') {
            //             // Warna untuk grup kualitas (afkir, super, dll)
            //             $sheet->getDelegate()->getStyle("A$row:G$row")->getFont()->setBold(true);
            //             $sheet->getDelegate()->getStyle("A$row:G$row")->getFill()
            //                 ->setFillType('solid')
            //                 ->getStartColor()->setRGB('A9A9A9'); // abu gelap
            //         }
            //     }
            // }
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $delegate = $sheet->getDelegate();

                // Bold untuk info di atas
                $delegate->getStyle('A1:B3')->getFont()->setBold(true);

                // Bold untuk header tabel
                $delegate->getStyle('A5:G5')->getFont()->setBold(true);

                $highestRow = $delegate->getHighestRow();

                for ($row = 6; $row <= $highestRow; $row++) {
                    $firstCell = $delegate->getCell("A$row")->getValue();
                    $secondCell = $delegate->getCell("B$row")->getValue();
                    $fourthCell = $delegate->getCell("D$row")->getValue();

                    // Cari baris dengan teks 'Total PPh'
                    if (is_string($firstCell) && stripos($firstCell, 'total pph') !== false) {
                        // Ubah warna font jadi merah di kolom E (nominal PPh)
                        $delegate->getStyle("E$row")->getFont()->getColor()->setRGB('FF0000');
                        $delegate->getStyle("E$row")->getFont()->setBold(true);
                    }

                    // Warna untuk subtotal dan grand total
                    if (is_string($firstCell) && stripos($firstCell, 'total') !== false) {
                        $delegate->getStyle("A$row:G$row")->getFont()->setBold(true);
                        $delegate->getStyle("A$row:G$row")->getFill()
                            ->setFillType('solid')
                            ->getStartColor()->setRGB('D3D3D3'); // abu terang
                    } elseif ($secondCell === null || $secondCell === '') {
                        // Warna untuk grup kualitas (afkir, super, dll)
                        $delegate->getStyle("A$row:G$row")->getFont()->setBold(true);
                        $delegate->getStyle("A$row:G$row")->getFill()
                            ->setFillType('solid')
                            ->getStartColor()->setRGB('A9A9A9'); // abu gelap
                    }
                }
            }

        ];
    }
}
