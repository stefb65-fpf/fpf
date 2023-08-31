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
use App\Models\Personne;
use App\Models\Ur;
use App\Models\Utilisateur;
use http\Env\Request;
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
        return [$club, $activites, $equipements, $countries];
    }

    public function updateClubGeneralite(Club $club, $request)
    {
        $error = null;
        if ($_FILES['logo']['name'] != '') {
            // une image a été envoyé, on change donc le media du slider
            list($first, $extension) = explode('.', $_FILES['logo']['name']);
            $name = 'club-' . uniqid();
            $dir = storage_path() . '/app/public/uploads/clubs/' . $club->numero;
            $target_file = $dir . '/' . $name . '.' . $extension;
            $size = $_FILES['logo']['size'];
            $authrorized_extensions = array('jpeg', 'jpg', 'png');
            if (!in_array($extension, $authrorized_extensions)) {
                $error = 1;
            }
            if ($size > 1048576) {
                $error = 2;
            }
            if (!$error) {
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir);
                }
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                    $request->logo = $name . '.' . $extension;
                }
            }
        }
        if ($error) {
            return $error;
        } else {
            $datap = array('nom' => $request->nom, 'courriel' => $request->courriel, 'web' => $request->web, "logo" => $request->logo);
            $club = Club::where('id', $club->id)->first();
            $club->update($datap);
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user, "Modification des informations générales du club \"" . $club->nom . "\"");
            }
        }
    }

    public function updateClubAdress(Club $club, $request)
    {
        $selected_pays = Pays::where('id', $request->pays)->first();
        $datap_adresse = $request->all();
        unset($datap_adresse['_token']);
        unset($datap_adresse['_method']);
        unset($datap_adresse['enableBtn']);
        $datap_adresse['pays'] = $selected_pays->nom;
//        dd($datap_adresse);
        $indicatif = $selected_pays->indicatif;

        //contrôle et formatage des numéros de téléphone
        $telephonedomicile = $this->format_fixe_for_base($datap_adresse["telephonedomicile"], $indicatif);
        if ($telephonedomicile == -1) {
            return '2';
        }
        $telephonemobile = $this->format_mobile_for_base($datap_adresse["telephonemobile"], $indicatif);
        if ($telephonemobile == -1) {
            return '1';
        }
        $datap_adresse["telephonedomicile"] = $telephonedomicile;
        $datap_adresse["telephonemobile"] = $telephonemobile;

        $datap_adresse['pays'] = $selected_pays->nom;
//        dd($datap_adresse);
        if (!$club->adresses_id) { //le club n'a aucune adresse en base. On en crée une.
            $new_adresse = Adresse::create($datap_adresse);
        } else { //la club a déjà une adresse en base. On met à jour l'adresse par defaut.
            $club->adresse->update($datap_adresse);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user, "Modification de l'adresse du club \"" . $club->nom . "\"");
        }
        return '0';
    }

    public function updateClubReunion(Club $club, $request)
    {
        $datap = $request->all();
        unset($datap['_token']);
        unset($datap['_method']);
        unset($datap['enableBtn']);
//        dd($datap);
        $club->update($datap);
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user, "Modification des réunions du club \"" . $club->nom . "\"");
        }
    }

    protected function updateClubAdherent($request, $utilisateur)
    {
        $pays = Pays::where('id', $request->pays)->first();
        if (!$pays) {
            return false;
        }

        try {
            DB::beginTransaction();
            // on récupère les infos personne à mettre à jour
            $personne = $utilisateur->personne;
            $datap = $request->only('nom', 'prenom', 'datenaissance', 'sexe');
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
            if ($phone_mobile == -1) {
                DB::rollBack();
                return false;
            }
            $datap['phone_mobile'] = $phone_mobile;
            $datap['news'] = $request->news ? 1 : 0;
            $personne->update($datap);

            // on récupère les infos adresse à mettre à jour
            $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville');
            $dataa['pays'] = $pays->nom;
            $telephonedomicile = $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif);
            if ($telephonedomicile == -1) {
                DB::rollBack();
                return false;
            }
            $dataa['telephonedomicile'] = $telephonedomicile;
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

    protected function storeClubAdherent($request, $club)
    {
        if ($request->personne_id != null) {
            $personne = Personne::where('id', $request->personne_id)->first();
            if (!$personne) {
                return false;
            }
        } else {
            if (!filter_var(trim($request->email), FILTER_VALIDATE_EMAIL)) {
                return false;
            }
            $pays = Pays::where('id', $request->pays)->first();
            if (!$pays) {
                return false;
            }
            $news = $request->news ? 1 : 0;
            $password = $this->generateRandomPassword();
            $datap = array(
                'nom' => trim(strtoupper($request->nom)),
                'prenom' => trim($request->prenom),
                'sexe' => $request->sexe,
                'email' => trim($request->email),
                'password' => $password,
                'datenaissance' => $request->datenaissance,
                'news' => $news,
                'is_adherent' => 1,
                'premiere_connexion' => 1
            );
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
            if ($phone_mobile == -1) {
                return false;
            }
            $telephonedomicile = $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif);
            if ($telephonedomicile == -1) {
                return false;
            }
            $datap['phone_mobile'] = $phone_mobile;

            $personne = Personne::create($datap);

            $this->insertWpUser(trim($request->nom), trim($request->prenom), trim($request->email), $password);

            // on crée l'adresse
            $dataa = array(
                'libelle1' => $request->libelle1,
                'libelle2' => $request->libelle2,
                'codepostal' => $request->codepostal,
                'ville' => strtoupper($request->ville),
                'pays' => $pays->nom,
                'telephonedomicile' => $telephonedomicile
            );
            $adresse = Adresse::create($dataa);

            // on lie l'adresse à la personne
            $personne->adresses()->attach($adresse->id);
        }

        // on cherche le max numeroutilisateur pour le club
        $max_numeroutilisateur = Utilisateur::where('clubs_id', $club->id)->max('numeroutilisateur');
        $numeroutilisateur = $max_numeroutilisateur + 1;
        $identifiant = str_pad($club->urs_id, 2, '0', STR_PAD_LEFT) . '-'
            . str_pad($club->numero, 4, '0', STR_PAD_LEFT) . '-'
            . str_pad($numeroutilisateur, 4, '0', STR_PAD_LEFT);

        // on calcule le ct par défaut avec la date de naissance
        // on calcule l'âge de la personne à partir de sa date de naissance
        $date_naissance = new \DateTime($request->datenaissance);
        $date_now = new \DateTime();
        $age = $date_now->diff($date_naissance)->y;
        $ct = 2;
        if ($age < 18) {
            $ct = 4;
        } else {
            if ($age < 25) {
                $ct = 3;
            }
        }

        // on crée un nouvel utilisateur pour la personne dans le club
        $datau = array(
            'personne_id' => $personne->id,
            'urs_id' => $club->urs_id,
            'clubs_id' => $club->id,
            'adresses_id' => $personne->adresses[0]->id,
            'identifiant' => $identifiant,
            'numeroutilisateur' => $numeroutilisateur,
            'sexe' => $request->sexe,
            'nom' => trim(strtoupper($request->nom)),
            'prenom' => trim($request->prenom),
            'ct' => $ct,
            'statut' => 0
        );
        Utilisateur::create($datau);
        return true;
    }
}
