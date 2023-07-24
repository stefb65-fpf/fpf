<?php

namespace App\Http\Controllers;

use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubAbonnementRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Models\Activite;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Equipement;
use App\Models\Pays;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    use Tools;
    use ClubTools;
    public function __construct()
    {
        $this->middleware(['checkLogin', 'clubAccess']);
    }

    public function gestion()
    {
        $club = $this->getClub();
        return view('clubs.gestion', compact('club'));
    }

    public function gestionAdherents($statut = null,$abonnement = null)
    {
        $club = $this->getClub();
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        $club->is_abonne = $club->numerofinabonnement >= $numeroencours;
        $club->numero_fin_reabonnement = $club->is_abonne ? $club->numerofinabonnement + 5 : $numeroencours + 5;
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0,1,2,3, 4])) {
            $query = $query->where('utilisateurs.statut', $statut);
        }
        if (in_array($abonnement, [0,1])) {
            $query = $query->where('personnes.is_abonne', $abonnement);
        }
        $adherents = $query->get();
//        dd($adherents);
        foreach ($adherents as $adherent) {
            // si la personne est abonnée, on récupère le numéro de fin de son abonnement
            $adherent->fin = $adherent->personne->is_abonne ? $adherent->personne->abonnements->where('etat', 1)[1]['fin'] : '';
        }
        return view('clubs.gestion_adherents', compact('club','statut','abonnement','adherents'));
    }

    public function infosClub()
    {
        $club = $this->getClub();
        list($club,$activites,$equipements,$countries) = $this->getClubFormParameters($club);
        return view('clubs.infos_club', compact('club', 'activites', 'equipements', 'countries'));
    }

    public function gestionFonctions()
    {
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
            ->selectRaw('fonctions.id, fonctions.libelle, utilisateurs.identifiant, personnes.nom, personnes.prenom , utilisateurs.id as id_utilisateur')
            ->orderBy('fonctions.ordre')
            ->get();
        $tab_fonctions = [];
        foreach ($fonctions as $fonction) {
            $tab_fonctions[$fonction->id] = $fonction;
        }

        return view('clubs.gestion_fonctions', compact('club', 'adherents', 'tab_fonctions'));
    }

    public function gestionReglements()
    {
        $club = $this->getClub();
        // on récupère tous les règlements du club
        $reglements = Reglement::where('clubs_id', $club->id)->orderByDesc('id')->get();
        $dir = $club->getImageDir();
        list($tmp, $dir_club) = explode('htdocs/', $dir);
        $dir_club = env('APP_URL') . '/' . $dir_club;
        return view('clubs.gestion_reglements', compact('club', 'reglements', 'dir_club', 'dir'));
    }

    protected function getClub()
    {
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

    public function updateGeneralite( ClubReunionRequest $request, Club $club)
    {

        $this->updateClubGeneralite($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations générales du club ont été mises à jour");

    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $this->updateClubAdress($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations de réunion du club ont été mises à jour");
    }

    public function updateFonction(Request $request, $current_utilisateur_id, $fonction_id)
    {
//        dd($request, $current_utilisateur_id,$fonction_id);
        //on vérifie que le nouvel utilisateur appartient au club
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id')
            ->get();
        $in_array = false;
        $new_utilisateur_id = $request->adherent_id;
        foreach ($adherents as $adherent) {
            if ($adherent->id == $new_utilisateur_id) {
                $in_array = true;
            }
        }
        if (!$in_array) {
            return redirect()->route('clubs.gestion_fonctions')->with('error', 'Cet utilisateur ne fait pas partie des adhérent du club');
        }
        //on ajoute la ligne correspondant à la table pivot
        $data_ap = array('utilisateurs_id' => $new_utilisateur_id, 'fonctions_id' => $fonction_id);
        DB::table('fonctionsutilisateurs')->insert($data_ap);
        //on supprime l'ancien utilisateur
        DB::table('fonctionsutilisateurs')->where("utilisateurs_id", $current_utilisateur_id)->where("fonctions_id", $fonction_id)->delete();
        return redirect()->route('clubs.gestion_fonctions')->with('success', "La fonction a été attribuée à un nouvel utilisateur");
    }

    public function addFonction(Request $request, $fonction_id)
    {
//        dd($request, $fonction_id);
        //on vérifie que le nouvel utilisateur appartient au club
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->get();
        $in_array = false;
        $new_utilisateur_id = $request->adherent_id;
        foreach ($adherents as $adherent) {
            if ($adherent->id == $new_utilisateur_id) {
                $in_array = true;
            }
        }
        if (!$in_array) {
            return redirect()->route('clubs.gestion_fonctions')->with('error', 'Cet utilisateur ne fait pas partie des adhérent du club');
        }
        //on ajoute la ligne correspondant à la table pivot
        $data_ap = array('utilisateurs_id' => $new_utilisateur_id, 'fonctions_id' => $fonction_id);
        DB::table('fonctionsutilisateurs')->insert($data_ap);
        return redirect()->route('clubs.gestion_fonctions')->with('success', "La fonction a été ajoutée à cet utilisateur");
    }

    public function deleteFonction($current_utilisateur_id, $fonction_id)
    {
//        dd($current_utilisateur_id, $fonction_id);
        DB::table('fonctionsutilisateurs')->where("utilisateurs_id", $current_utilisateur_id)->where("fonctions_id", $fonction_id)->delete();
        return redirect()->route('clubs.gestion_fonctions')->with('success', "La fonction a été ôtée à cet utilisateur");
    }
}
