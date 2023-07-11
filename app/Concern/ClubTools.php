<?php

namespace App\Concern;

use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Models\Activite;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Equipement;
use App\Models\Pays;
use Illuminate\Support\Facades\DB;

trait ClubTools
{
    public function getClubFormParameters(Club $club)
    {
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
        if (sizeof($tab) > 1) {
            $club->adresse->telephonedomicile = $tab[1];
        }
        $club->adresse->telephonedomicile = ltrim($club->adresse->telephonedomicile, '0');

        if ($club->adresse->indicatif_fixe == "33") {
            $club->adresse->telephonedomicile = "0" . $club->adresse->telephonedomicile;
        }
        $club->adresse->indicatif_mobile = "";
        $tab = explode('.', $club->adresse->telephonemobile);
        if (sizeof($tab) > 1) {
            $club->adresse->telephonemobile = $tab[1];
        }
        $first_number = substr($club->adresse->telephonemobile, 0, 1);
//        dd($first_number);
        if ($first_number == "6" || $first_number == "7") {
//            $club->adresse->telephonemobile = "0". $club->adresse->telephonemobile ;
            $club->adresse->telephonemobile = chunk_split("0" . $club->adresse->telephonemobile, 2, ' ');
        }
        //gestion abonnement
        $currentNumber = Configsaison::where('id', 1)->first()->numeroencours;
//        dd($currentNumber);
        $club->is_abonne = true;
        if ($club->numerofinabonnement < $currentNumber) {
            $club->is_abonne = false;
        }
        $countries = Pays::all();
        return[ $club, $activites, $equipements ,$countries];
    }
    public function updateClubGeneralite(Club $club, $request){
        $logo = $club->logo;
        if ($request->logo) {
            $logo = $request->logo;
//           dd($logo);
        }
        $datap = array('nom' => $request->nom, 'courriel' => $request->courriel, 'web' => $request->web, "logo" => $logo);
        $club = Club::where('id', $club->id)->first();
        $club->update($datap);
    }
    public function updateClubAdress(Club $club,$request)
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
        if (!$club->adresses_id) { //le club n'a aucune adresse en base. On en crée une.
            $new_adresse = Adresse::create($datap_adresse);
        } else { //la club a déjà une adresse en base. On met à jour l'adresse par defaut.
            $club->adresse->update($datap_adresse);
        }
    }

    public function updateClubReunion( Club $club,$request)
    {
        $datap = $request->all();
        unset($datap['_token']);
        unset($datap['_method']);
        unset($datap['enableBtn']);
//        dd($datap);
        $club->update($datap);
    }
}
