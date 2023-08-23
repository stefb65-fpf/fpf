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
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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

        if ($club->adresse->indicatif_fixe == "33" && strlen($club->adresse->telephonedomicile)) {
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
        $club->is_abonne = $club->numerofinabonnement < $currentNumber;
        $countries = Pays::all();
        return[ $club, $activites, $equipements ,$countries];
    }
    public function updateClubGeneralite(Club $club, $request){

        if ($_FILES['logo']['name'] != '') {
            // une image a été envoyé, on change donc le media du slider
            list($first, $extension) = explode('.', $_FILES['logo']['name']);
            $name ='club-'.uniqid();
            $dir = storage_path().'/app/public/uploads/clubs/'.$club->numero;
            $target_file = $dir .'/'. $name . '.' . $extension;
            $size = $_FILES['logo']['size'];
            $authrorized_extensions = array('jpeg', 'jpg', 'png');
            if (!in_array($extension, $authrorized_extensions)) {
                return redirect()->back()->with('error', "L'image n'est pas au bon format. Veuillez télécharger une image au format .jpeg, .jpg ou .png");
            }
            if ($size > 1048576) {
                return redirect()->back()->with('error', "L'image est trop grande. Veuillez télécharger une image de taille maximum de 1 Mo ");
            }
            if(!File::isDirectory($dir)){
                File::makeDirectory($dir);
            }
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $request->logo = $name . '.' . $extension;
            }
        }
        $datap = array('nom' => $request->nom, 'courriel' => $request->courriel, 'web' => $request->web, "logo" => $request->logo);
        $club = Club::where('id', $club->id)->first();
        $club->update($datap);
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification des informations générales du club \"".$club->nom."\"");
        }
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
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification de l'adresse du club \"".$club->nom."\"");
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
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification des réunions du club \"".$club->nom."\"");
        }
    }

    protected function updateClubAdherent($request, $utilisateur) {
        $pays = Pays::where('id', $request->pays)->first();
        if (!$pays) {
            return false;
        }

        try {
            DB::beginTransaction();
            // on récupère les infos personne à mettre à jour
            $personne = $utilisateur->personne;
            $datap = $request->only('nom', 'prenom', 'datenaissance', 'phone_mobile', 'sexe');
            $datap['phone_mobile'] = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
            $datap['news'] = $request->news ? 1 : 0;
            $personne->update($datap);

            // on récupère les infos adresse à mettre à jour
            $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville');
            $dataa['pays'] = $pays->nom;
            $dataa['telephonedomicile'] = $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif);
            $adresse = $personne->adresses[0];
            $adresse->update($dataa);

            // on récupère les infos adresse2 à mettre à jour
            if (sizeof($personne->adresses) > 1) {
                $dataa2 = [];
                $dataa2['libelle1'] = $request->adresse2_libelle1;
                $dataa2['libelle2'] = $request->adresse2_libelle2;
                $dataa2['codepostal'] = $request->adresse2_codepostal;
                $dataa2['ville'] = $request->adresse2_ville;
                $pays2 = Pays::where('id', $request->adresse2_pays)->first();
                $dataa2['telephonedomicile'] = $this->format_fixe_for_base($request->adresse2_telephonedomicile, $pays2->indicatif);
                $dataa2['pays'] = $pays2->nom;

                $adresse2 = $personne->adresses[1];
                $adresse2->update($dataa2);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
