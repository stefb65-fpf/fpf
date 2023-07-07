<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RoutageFpExport;
use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PublicationController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index()
    {
        return view('admin.publications.index');
    }

    public function routageFP()
    {
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
        $numeroencours = $config->numeroencours;

        // on cherche le nombre d'abonnements où l'état est à 1
        $nbabos = Abonnement::where('etat', 1)->count();
        $nbclubsAbos = Club::where('numerofinabonnement', '>=', $numeroencours)->count();

        return view('admin.publications.routageFP', compact('nbabos', 'nbclubsAbos', 'numeroencours'));
    }

    public function generateRoutageFp($validate = 0)
    {
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
        $numeroencours = $config->numeroencours;

        $fichier = 'routageFP' . $numeroencours . '.xls';
        if (Excel::store(new RoutageFpExport($numeroencours), $fichier, 'xls')) {
            $file_to_download = env('APP_URL').'storage/app/public/xls/'.$fichier;

            if ($validate == 1) {
                // on change l'éta des abonnments dont le dernier numéro est celui en cours
                $dataa = array('etat' => 2);
                Abonnement::where('fin', $numeroencours)->update($dataa);

                // on met à jour le numéro de fichier
                $datac = array('numeroencours' => $numeroencours + 1);
                Configsaison::where('id', 1)->update($datac);
            }

            return redirect()->route('admin.routage.france_photo')->with('success', "Le fichier Excel a bien été généré et peut être téléchargé en cliquant sur le lien ci-dessous. <br><a href='$file_to_download' target='_blank'>Télécharger le fichier Excel</a>");
        } else {
            return redirect()->route('admin.routage.france_photo')->with('error', "Une erreur est survenue lors de la génération du fichier Excel.");
        }
    }

    public function routageFede()
    {
        return view('admin.publications.routageFede');
    }

    public function etiquettes()
    {
        return view('admin.publications.etiquettes');
    }

    public function emargements()
    {
        return view('admin.publications.emargements');
    }
}
