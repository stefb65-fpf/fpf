<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecapFormations implements FromView, WithStyles, ShouldAutoSize
{
    private $formations;

    public function __construct($formations)
    {
        $this->formations = $formations;
    }

    public function view(): View
    {
        $formations = $this->formations;
        return view('exports.recapFormations', compact('formations'));
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        return [
            // Style the first row as bold text.
            1 => [
                'font' => ['bold' => true]
            ],
            'C:G' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
            'A' => [
                'font' => ['bold' => true]
            ]
        ];
    }
}
