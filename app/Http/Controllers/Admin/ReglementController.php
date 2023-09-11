<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReglementController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index($term=null)
    {
        $query = Reglement::orderByDesc('reglements.id');
        if($term){
            $this->getReglementsByTerm($term, $query);
        }
        $reglements = $query->paginate(100);
        foreach ($reglements as $reglement) {
            if ($reglement->clubs_id) {
                $club = Club::where('id', $reglement->clubs_id)->first();
                if ($club) {
                    $dir = $club->getImageDir();
                    if (file_exists($dir.'/'.$reglement->reference.'.pdf')) {
                        list($tmp, $dir_club) = explode('htdocs/', $dir);
                        $reglement->bordereau = env('APP_URL').$dir_club.'/'.$reglement->reference.'.pdf';
                    }
                    $reglement->nom_club = $club->nom;
                }
            } else {
                $reglement->nom = '';
                foreach($reglement->utilisateurs as $utilisateur) {
                    $reglement->nom .= $utilisateur->personne->nom.' '.$utilisateur->personne->prenom.' ';
                }
            }
        }

        return view('admin.reglements.index', compact('reglements', 'term'));
    }

    public function editionCartes() {
        $utilisateurs = Utilisateur::where('statut', 2)->whereNotNull('personne_id')->where('urs_id', '<>', 0)->orderBy('clubs_id')->get();
        return view('admin.reglements.cartes', compact('utilisateurs'));
    }

    public function historiqueCartes() {
        // on parcourt tout le répertoire des cartes pour l'année en cours
        $dir = storage_path().'/app/public/cartes/'.date('Y').'/';
        $href = env('APP_URL').'/storage/app/public/cartes/'.date('Y').'/';
        $min = (intval(date('m')) > 8) ? 9 : 1;
        $tab_files = [];
        for ($i = intval(date('m')); $i >= $min; $i--) {
            // on parcourt tous les fichiers du répertoire
            $repertoire = opendir($dir.str_pad($i, 2, '0', STR_PAD_LEFT));
            // Boucle pour lire le répertoire ligne par ligne
            while($fichier = readdir($repertoire)) {
                // Stockage nom fichier dans un tableau des fichiers pdf, sauf les rééditions
                if ($fichier != '' && $fichier != '.' && $fichier != '..'){
                    $tab_file = explode('.', $fichier);
                    $indice = substr($tab_file[0], -14, 14);
                    $type = '';
                    if (substr($tab_file[0], 0, 6) == 'cartes') {
                        $type = 'cartes';
                    } elseif (substr($tab_file[0], 0, 31) == 'etiquettes_renouvellement_clubs') {
                        $type = 'clubs';
                    } elseif (substr($tab_file[0], 0, 37) == 'etiquettes_renouvellement_individuels') {
                        $type = 'indiv';
                    }
                    if ($type != '') {
                        $tab_files[$indice][$type] = $fichier;
                        $tab_files[$indice]['path'] = $href.str_pad($i, 2, '0', STR_PAD_LEFT);
                    }
                }
            }
        }
        krsort($tab_files);
        return view('admin.reglements.historiqueCartes', compact('tab_files'));
    }
}
