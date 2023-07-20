<?php

namespace App\Http\Controllers\Api;


use App\Exports\RoutageListAdherents;
use App\Http\Controllers\Controller;

use App\Models\Adresse;
use App\Models\Club;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdherentsList extends Controller
{
    public function createListAdherents(Request $request)
    {
        $tab_adherents = array();
        $club = $request->club;
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club)->orderBy('utilisateurs.identifiant');
        $utilisateurs = $query->get();

        foreach ($utilisateurs as $utilisateur) {
            $personne = Personne::where('id', $utilisateur->personne_id)->first();
            if ($personne) {
                $personne->identifiant = $utilisateur->identifiant;
                $personne->statut = $utilisateur->statut;
                $numero_fin =$utilisateur->personne->abonnements->where('etat', 1);
                if($utilisateur->personne->is_abonne){
                    if(isset($numero_fin[0])){
                        $personne->fin = $numero_fin[0]['fin'];
                    }else{
                        $personne->fin = $numero_fin[1]['fin'];
                    }
                }else{
                    $personne->fin ="";
                }
                $tab_adherents[] = $personne;
            }
        }
//        dd($tab_adherents);
        foreach ($tab_adherents as $adherent) {
            $adresse = Adresse::join('adresse_personne', 'adresse_personne.adresse_id', '=', 'adresses.id')
                ->where('adresse_personne.personne_id', $adherent->id)
                ->orderByDesc('adresse_personne.defaut')
                ->selectRaw('adresses.libelle1, adresses.libelle2, adresses.codepostal, adresses.ville, adresses.pays')
                ->first();
            $adherent->adresse = $adresse;
        }
        $fichier = 'routage_' . date('YmdHis') . '.xls';
        if (Excel::store(new RoutageListAdherents($tab_adherents), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            return new JsonResponse(['file' => $file_to_download], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
        }
    }
}
