<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InscritExport  implements FromView, WithStyles, ShouldAutoSize
{
    private $inscrits;

    public function __construct($inscrits)
    {
        $this->inscrits = $inscrits;
    }

    public function view(): View
    {
        $inscrits = $this->inscrits;
        return view('exports.inscritsListe', compact('inscrits'));
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        return [
            // Style the first row as bold text.
            1 => [
                'font' => ['bold' => true]
            ],

        ];
    }
}
