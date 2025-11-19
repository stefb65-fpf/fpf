<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormationsListeExport  implements FromView, WithStyles, ShouldAutoSize, WithEvents
{
    private $formations;

    public function __construct($formations)
    {
        $this->formations = $formations;
    }

    public function view(): View
    {
        $formations = $this->formations;
        return view('exports.formationsListe', compact('formations'));
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(30);
        $lastRow = 1; // si tu veux le calculer dynamiquement, tu peux le faire via count()
        foreach ($this->formations as $formation) {
            $lastRow += $formation->sessions->count();
        }

        $sheet->getStyle("F2:K{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00 [$€-fr-FR]');

        $sheet->getStyle("F1:M{$lastRow}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        return [
            // Ligne 1 : en-tête en gras
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                ],
            ],
            "A1:D{$lastRow}" => [
                'alignment' => [
                    'vertical'   => 'top',   // ✅ Alignement vertical haut
                    'horizontal' => 'left',  // ✅ Alignement horizontal gauche
                    'wrapText'   => true,    // ✅ Retour à la ligne automatique
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $row = 2; // en partant de la 2e ligne (si la 1ère est l'entête)
                foreach ($this->formations as $formation) {
                    $sessionsCount = $formation->sessions->count();

                    if ($sessionsCount > 1) {
                        // Fusion sur la colonne A (nom de la formation)
                        $sheet->mergeCells("A{$row}:A" . ($row + $sessionsCount - 1));

                        // Fusion sur la colonne B (formateurs)
                        $sheet->mergeCells("B{$row}:B" . ($row + $sessionsCount - 1));
                    }

                    // Passer aux lignes suivantes
                    $row += $sessionsCount;
                }

                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(50);

                $sheet->getColumnDimension('B')->setAutoSize(false);
                $sheet->getColumnDimension('B')->setWidth(30);
            },
        ];
    }
}
