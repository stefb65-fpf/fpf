<?php

namespace App\Exports;

use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Utilisateur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class RoutageFedeExport implements FromView, WithStyles, ShouldAutoSize
{
    public function __construct(array $personnes)
    {
        $this->personnes = $personnes;
    }

    public function view(): View
    {
        $personnes = $this->personnes;
        return view('exports.routageFede', compact('personnes'));
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        return [
            // Style the first row as bold text.
            1    => [
                'font' => ['bold' => true]
            ],
//            // Styling an entire column.

//            // Styling a specific cell by coordinate.
//            'B2' => ['font' => ['italic' => true]],

//            'C'  => ['font' => ['size' => 16]],
        ];
    }
}
