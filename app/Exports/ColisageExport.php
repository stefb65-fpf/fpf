<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColisageExport implements FromView, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    private $souscriptions;

    public function __construct($souscriptions)
    {
        $this->souscriptions = $souscriptions;
    }

    public function view(): View
    {
        $souscriptions = $this->souscriptions;
        return view('exports.colisageListe', compact('souscriptions'));
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        return [
            // Style the first row as bold text.
            1    => [
                'font' => ['bold' => true]
            ],

        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
