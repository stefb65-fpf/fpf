<?php

namespace App\Concern;

use App\Models\Adresse;
use App\Models\Pays;
use App\Models\Ur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait UrTools
{
    public function updateUrInformations(Ur $ur, $request){
        $datap_info = $request->only('nom','courriel', 'web');
        $ur->update($datap_info);


        $selected_pays = Pays::where('id', $request->pays)->first();
        $datap_adresse = $request->only('libelle1','libelle2','codepostal','ville','pays','telephonedomicile','telephonemobile');
        $datap_adresse['pays'] = $selected_pays->nom;
        $indicatif = $selected_pays->indicatif;
        $datap_adresse["telephonedomicile"] =$this->format_fixe_for_base($datap_adresse["telephonedomicile"],$indicatif) ;
        $datap_adresse["telephonemobile"] =$this->format_mobile_for_base($datap_adresse["telephonemobile"]);
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
            $this->MailAndHistoricize($user,"Modification de l'UR \"".$ur->nom."\"");
        }
    }
    public function getUrInformations(Ur $ur){
        $ur->departements = DB::table('departementsurs')->where('urs_id', $ur->id)->get();
        $ur->adresse->telephonemobile = $this->format_phone_number_visual($ur->adresse->telephonemobile);
        $ur->adresse->telephonedomicile = $this->format_phone_number_visual($ur->adresse->telephonedomicile);
        $ur->adresse->indicatif_fixe = Pays::where('nom', $ur->adresse->pays)->first()->indicatif;

        return $ur;
    }
}
