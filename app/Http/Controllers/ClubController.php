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

    public function gestionAdherents()
    {
        $club = $this->getClub();
        return view('clubs.gestion_adherents', compact('club'));
    }

    public function infosClub()
    {
        $club = $this->getClub();
        $result = $this->getClubFormParameters($club);
        $club = $result[0];
        $activites = $result[1];
        $equipements = $result[2];
        $countries = $result[3];
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
        return view('clubs.gestion_reglements', compact('club'));
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

    public function updateGeneralite( Request $request, Club $club)
    {
//        dd($request);
//        if (!empty($_FILES) && $_FILES['image']['size'] > 0) {
//            // le post a une image
//            $this->saveMediaForPost($post);
//        }
        //TODO : enregistrer file sur le serveur
        $logo = $club->logo;
        if ($request->logo) {
            $logo = $request->logo;
//           dd($logo);
        }
        $datap = array('nom' => $request->nom, 'courriel' => $request->courriel, 'web' => $request->web, "logo" => $logo);
        $club = Club::where('id', $club->id)->first();
        $club->update($datap);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations générales du club ont été mises à jour");

    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {

        $selected_pays = Pays::where('id', $request->pays)->first();
        $datap_adresse = $request->all();
        unset($datap_adresse['_token']);
        unset($datap_adresse['_method']);
        unset($datap_adresse['enableBtn']);
        $datap_adresse['pays'] = $selected_pays->nom;
//        dd($datap_adresse);
        $indicatif = $selected_pays->indicatif;
        $datap_adresse["telephonedomicile"] =$this->format_fixe_for_base($datap_adresse["telephonedomicile"],$indicatif) ;
        $datap_adresse["telephonemobile"] =$this->format_mobile_for_base($datap_adresse["telephonemobile"]);
        $datap_adresse['pays'] = $selected_pays->nom;
//        dd($datap_adresse);
;
        if (!$club->adresses_id) { //le club n'a aucune adresse en base. On en crée une.
            $new_adresse = Adresse::create($datap_adresse);
        } else { //la club a déjà une adresse en base. On met à jour l'adresse par defaut.
            $club->adresse->update($datap_adresse);
        }
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");

    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $datap = $request->all();
        unset($datap['_token']);
        unset($datap['_method']);
        unset($datap['enableBtn']);
//        dd($datap);
        $club->update($datap);
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");
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
