<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Club;
use App\Models\Equipement;
use App\Models\Fonction;
use App\Models\Pays;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'clubAccess']);
    }

    public function gestion() {
        $club = $this->getClub();
        return view('clubs.gestion', compact('club'));
    }

    public function gestionAdherents() {
        $club = $this->getClub();
        return view('clubs.gestion_adherents', compact('club'));
    }

    public function infosClub() {
        $club = $this->getClub();
        $activites = Activite::all();
        $equipements = Equipement::all();

        $club_equipements = DB::table('equipementsclubs')->where('clubs_id', $club->id)->get();
        $tab_equipements = [];
        foreach ($club_equipements as $v) {
            $tab_equipements[] = $v->equipements_id;
        }
        $club->equipements = $tab_equipements;

        $club_activites = DB::table('activitesclubs')->where('clubs_id', $club->id)->selectRaw('activites_id')->get();
        $tab_activites = [];
        foreach ($club_activites as $v) {
            $tab_activites[] = $v->activites_id;
        }
        $club->activites = $tab_activites;

        if($club->adresse->pays){
            $country = Pays::where('nom', strtoupper(strtolower($club->adresse->pays)))->first();
            $club->adresse->indicatif =$country->indicatif;
//                dd( $adresse->indicatif);
        }else{
            $club->adresse->indicatif ="";
        }

    $countries = Pays::all();
        return view('clubs.infos_club', compact('club', 'activites', 'equipements', 'countries'));
    }

    public function gestionFonctions() {
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->get();

        // on récupère les fonctions du club
        $fonctions = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->where('fonctions.instance', 3)
            ->selectRaw('fonctions.id, fonctions.libelle, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->orderBy('fonctions.ordre')
            ->get();

        return view('clubs.gestion_fonctions', compact('club', 'adherents', 'fonctions'));
    }

    public function gestionReglements() {
        $club = $this->getClub();
        return view('clubs.gestion_reglements', compact('club'));
    }

    protected function getClub() {
        $cartes = session()->get('cartes');
        if (!$cartes || count($cartes) == 0) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations club");
        }
        $active_carte = $cartes[0];
        $club_id = $active_carte->clubs_id;
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations club");
        }
        return $club;
    }
}
