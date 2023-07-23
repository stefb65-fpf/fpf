<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class RoutageListAdherents implements FromView, WithStyles, ShouldAutoSize
{
    public function __construct($utilisateurs)
    {
        $this->utilisateurs = $utilisateurs;
    }

    public function view(): View
    {
        $utilisateurs = $this->utilisateurs;
        return view('exports.routageListeAdherents', compact('utilisateurs'));
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
}
