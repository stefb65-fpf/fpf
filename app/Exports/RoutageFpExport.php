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


class RoutageFpExport implements FromView, WithStyles, ShouldAutoSize
{
    public function __construct(int $numeroencours)
    {
        $this->numeroencours = $numeroencours;
    }

    public function view(): View
    {
//        $period = $this->year.'-'.$this->month;
//        $orders = Order::where('created_at', 'LIKE', $period.'%')->whereNotIn('status', array(6,7))->get();
//        return view('exports.routageFP', [
//            'orders' => $orders
//        ]);
        // on rechreche tous les adhérents et abonnés seuls devant recevoir le numéro en cours
        $numeroencours = $this->numeroencours;
        $abonnes = Abonnement::where('etat', 1)->get();
        foreach ($abonnes as $abonne) {
            $personne = $abonne->personne;
            $adresse = Adresse::join('adresse_personne', 'adresse_personne.adresse_id', '=', 'adresses.id')
                ->where('adresse_personne.personne_id', $personne->id)
                ->orderByDesc('adresse_personne.defaut')
                ->selectRaw('adresses.libelle1, adresses.libelle2, adresses.codepostal, adresses.ville, adresses.pays')
                ->first();
            if (!$adresse) {
                $adresse = new Adresse();
                $adresse->libelle1 = '5 rue Jules Vallès';
                $adresse->libelle2 = '';
                $adresse->codepostal = '75011';
                $adresse->ville = 'PARIS';
                $adresse->pays = 'FRANCE';
            }
            $abonne->adresse = $adresse;
        }

        $clubs = Club::where('numerofinabonnement', '>=', $numeroencours)->orderBy('numero')->get();
        foreach($clubs as $club) {
            // on cherche le contac et son adresse
            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $club->id)
                ->selectRaw('id, identifiant, personne_id')
                ->first();
            if ($contact) {
                $personne = $contact->personne;
                if ($personne) {
                    $adresse = Adresse::join('adresse_personne', 'adresse_personne.adresse_id', '=', 'adresses.id')
                        ->where('adresse_personne.personne_id', $personne->id)
                        ->orderByDesc('adresse_personne.defaut')
                        ->selectRaw('adresses.libelle1, adresses.libelle2, adresses.codepostal, adresses.ville, adresses.pays')
                        ->first();
                    if (!$adresse) {
                        $adresse = new Adresse();
                        $adresse->libelle1 = '5 rue Jules Vallès';
                        $adresse->libelle2 = '';
                        $adresse->codepostal = '75011';
                        $adresse->ville = 'PARIS';
                        $adresse->pays = 'FRANCE';
                    }
                    $club->adresse = $adresse;
                    $club->personne = $personne;
                }

            }
        }
        return view('exports.routageFP', compact('abonnes', 'numeroencours', 'clubs'));
    }

//    public function export($year, $month)
//    {
//        return Excel::download(new RoutageFpExport($year, $month), 'routage.xlsx');
//    }

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
