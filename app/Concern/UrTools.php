<?php

namespace App\Concern;

use App\Models\Adresse;
use App\Models\Pays;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait UrTools
{
    public function updateUrInformations(Ur $ur, $request)
    {
        $datap_info = $request->only('nom', 'courriel', 'web');
        $ur->update($datap_info);
        $selected_pays = Pays::where('id', $request->pays)->first();
        $datap_adresse = $request->only('libelle1', 'libelle2', 'codepostal', 'ville', 'pays', 'telephonedomicile', 'telephonemobile');
        $datap_adresse['pays'] = $selected_pays->nom;
        $indicatif = $selected_pays->indicatif;

        //contrôle et formatage des numéros de téléphone
        $telephonedomicile = $this->format_fixe_for_base($datap_adresse["telephonedomicile"], $indicatif);
        $telephonemobile = $this->format_mobile_for_base($datap_adresse["telephonemobile"], $indicatif);
        if ($telephonedomicile == -1) {
            return '2';
        }
        if ($telephonemobile == -1) {
            return '1';
        }

        $datap_adresse["telephonedomicile"] = $telephonedomicile;
        $datap_adresse["telephonemobile"] = $telephonemobile;

        $datap_adresse['pays'] = $selected_pays->nom;
//        dd($datap_adresse);
        if (!$ur->adresses_id) { //l'ur n'a aucune adresse en base. On en crée une.
            $new_adresse = Adresse::create($datap_adresse);
        } else { //l'ur' a déjà une adresse en base. On met à jour l'adresse par defaut.
            $ur->adresse->update($datap_adresse);
        }
//        dd($datap);

        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user, "Modification de l'UR \"" . $ur->nom . "\"");
        }
        return '0';
    }

    public function getUrInformations(Ur $ur)
    {
        $ur->departements = DB::table('departements')->where('urs_id', $ur->id)->get();
//        $ur->departements = DB::table('departementsurs')->where('urs_id', $ur->id)->get();
        $ur->adresse->telephonemobile = $this->format_phone_number_visual($ur->adresse->telephonemobile);
        $ur->adresse->telephonedomicile = $this->format_phone_number_visual($ur->adresse->telephonedomicile);
        $ur->adresse->indicatif_fixe = Pays::where('nom', $ur->adresse->pays)->first()->indicatif;

        return $ur;
    }

    protected function updateFonctionUr($identifiant, $fonction, $ur_id) {
        if ($identifiant == '') {
            return '10';
//            return redirect()->route('urs.fonctions.change_attribution', $fonction)->with('error', "Vous devez saisir un identifiant");
        }
        $utilisateur = Utilisateur::where('identifiant', $identifiant)->first();
        if (!$utilisateur) {
            return '20';
//            return redirect()->route('urs.fonctions.change_attribution', $fonction)->with('error', "L'identifiant saisi n'est pas valide");
        }
        if ($utilisateur->urs_id != $ur_id) {
            return '30';
//            return redirect()->route('urs.fonctions.change_attribution', $fonction)->with('error', "L'adhérent doit faire partie de votre UR");
        }
        // on supprime l'ancienne attribution
        $old_utilisateur = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
            ->whereNotNull('utilisateurs.personne_id')
            ->where('utilisateurs.urs_id', $ur_id)
            ->first();
        if ($old_utilisateur) {
            DB::table('fonctionsutilisateurs')->where('fonctions_id', $fonction->id)->where('utilisateurs_id', $old_utilisateur->id)->delete();
        }

        // on ajoute la nouvelle attribution
        $datafu = array('fonctions_id' => $fonction->id, 'utilisateurs_id' => $utilisateur->id);
        DB::table('fonctionsutilisateurs')->insert($datafu);
        return '0';
    }
}
