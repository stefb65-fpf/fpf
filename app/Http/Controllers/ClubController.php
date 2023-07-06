<?php

namespace App\Http\Controllers;

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

class ClubController extends Controller
{
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
        if ($club->adresse->pays) {
            $country = Pays::where('nom', strtoupper(strtolower($club->adresse->pays)))->first();
            $club->adresse->indicatif_fixe = $country->indicatif;
//                dd( $adresse->indicatif);
        } else {
            $club->adresse->indicatif_fixe = "";
        }

        //gestion affichage telephone
        $tab = explode('.', $club->adresse->telephonedomicile);
//        dd($club->adresse->telephonefixe,  $tab);
        if(sizeof($tab)>1){
            $club->adresse->telephonedomicile = $tab[1];
        }
        $club->adresse->telephonedomicile = ltrim($club->adresse->telephonedomicile, '0');

        if($club->adresse->indicatif_fixe == "33"){
            $club->adresse->telephonedomicile = "0". $club->adresse->telephonedomicile ;
        }
        $club->adresse->indicatif_mobile = "";
      $tab = explode('.', $club->adresse->telephonemobile);
        if(sizeof($tab)>1){
            $club->adresse->telephonemobile = $tab[1];
        }
        $first_number = substr($club->adresse->telephonemobile, 0,1);
//        dd($first_number);
        if( $first_number == "6" || $first_number == "7"){
//            $club->adresse->telephonemobile = "0". $club->adresse->telephonemobile ;
            $club->adresse->telephonemobile = chunk_split("0". $club->adresse->telephonemobile , 2, ' ');
        }
        //gestion abonnement
        $currentNumber = Configsaison::where('id', 1)->first()->numeroencours;
//        dd($currentNumber);
        $club->is_abonne = true;
        if ($club->numerofinabonnement < $currentNumber) {
            $club->is_abonne = false;
        }
        $countries = Pays::all();
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
            ->selectRaw('fonctions.id, fonctions.libelle, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->orderBy('fonctions.ordre')
            ->get();

        return view('clubs.gestion_fonctions', compact('club', 'adherents', 'fonctions'));
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

    public function updateGeneralite(ClubReunionRequest $request, Club $club)
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
        if ($datap_adresse["telephonedomicile"]) {
            $datap_adresse["telephonedomicile"] = str_replace(" ","",$datap_adresse["telephonedomicile"]);
            $datap_adresse["telephonedomicile"] = ltrim($datap_adresse["telephonedomicile"], '0');
            $datap_adresse["telephonedomicile"] = '+' . $indicatif . '.' . $datap_adresse["telephonedomicile"];
        }
        if ($datap_adresse["telephonemobile"]) {
            $first_two_numbers = substr($datap_adresse["telephonemobile"], 0,2);
            if( $first_two_numbers == "06" || $first_two_numbers == "07"){
                //TODO: remove "0" and add "+33."
                $datap_adresse["telephonemobile"] = str_replace(" ","",$datap_adresse["telephonemobile"]);
                $datap_adresse["telephonemobile"] = ltrim($datap_adresse["telephonemobile"], '0');
                $datap_adresse["telephonemobile"] = '+33.' . $datap_adresse["telephonemobile"];
            }
        }
//        dd($datap_adresse);
        if (!$club->adresses_id) { //le club n'a aucune adresse en base. On en crée une.
            $new_adresse = Adresse::create($datap_adresse);
        } else { //la club a déjà une adresse en base. On met à jour l'adresse par defaut.
            $club->adresse->update($datap_adresse);
        }
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion( ClubReunionRequest $request, Club $club)
    {
        $datap = $request->all();
        unset($datap['_token']);
        unset($datap['_method']);
        unset($datap['enableBtn']);
//        dd($datap);
        $club->update($datap);
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");
    }
}
