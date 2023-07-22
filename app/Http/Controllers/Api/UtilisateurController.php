<?php

namespace App\Http\Controllers\Api;


use App\Exports\RoutageListAdherents;
use App\Http\Controllers\Controller;

use App\Models\Adresse;
use App\Models\Club;
use App\Models\Personne;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UtilisateurController extends Controller
{
    public function createListAdherents(Request $request)
    {
        $club = $request->club;
        $utilisateurs = Utilisateur::where('clubs_id', $club)->whereNotNull('personne_id')->orderBy('identifiant')->get();
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->personne->is_abonne) {
                $utilisateur->fin = isset($utilisateur->personne->abonnements->where('etat', 1)[0]) ?
                    $utilisateur->personne->abonnements->where('etat', 1)[0]->fin :
                    $utilisateur->personne->abonnements->where('etat', 1)[1]->fin;
            } else {
                $utilisateur->fin = '';
            }
        }
        $fichier = 'liste_adherents_' . date('YmdHis') . '.xls';
        if (Excel::store(new RoutageListAdherents($utilisateurs), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            return new JsonResponse(['file' => $file_to_download], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
        }




//        $tab_adherents = array();
//        $club = $request->club;
//        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
//            ->where('utilisateurs.clubs_id', $club)->orderBy('utilisateurs.identifiant');
//        $utilisateurs = $query->get();
//
//        foreach ($utilisateurs as $utilisateur) {
//            $personne = Personne::where('id', $utilisateur->personne_id)->first();
//            if ($personne) {
//                $personne->identifiant = $utilisateur->identifiant;
//                $personne->statut = $utilisateur->statut;
//                $numero_fin =$utilisateur->personne->abonnements->where('etat', 1);
//                if($utilisateur->personne->is_abonne){
//                    if(isset($numero_fin[0])){
//                        $personne->fin = $numero_fin[0]['fin'];
//                    }else{
//                        $personne->fin = $numero_fin[1]['fin'];
//                    }
//                }else{
//                    $personne->fin ="";
//                }
//                $tab_adherents[] = $personne;
//            }
//        }
//        foreach ($tab_adherents as $adherent) {
//            $adresse = Adresse::join('adresse_personne', 'adresse_personne.adresse_id', '=', 'adresses.id')
//                ->where('adresse_personne.personne_id', $adherent->id)
//                ->orderByDesc('adresse_personne.defaut')
//                ->selectRaw('adresses.libelle1, adresses.libelle2, adresses.codepostal, adresses.ville, adresses.pays')
//                ->first();
//            $adherent->adresse = $adresse;
//        }
//        $fichier = 'liste_adherents_' . date('YmdHis') . '.xls';
//        if (Excel::store(new RoutageListAdherents($tab_adherents), $fichier, 'xls')) {
//            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
//            return new JsonResponse(['file' => $file_to_download], 200);
//        } else {
//            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
//        }
    }

    public function checkRenouvellementAdherents(Request $request) {
        $club = Club::where('id', $request->club)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'impossible de récupérer le club'], 400);
        }
        $tab_adherents = [];
        foreach ($request->adherents as $adherent) {
            $utilisateur = Utilisateur::where('id', $adherent['id'])->first();
            if (!$utilisateur) {
                return new JsonResponse(['erreur' => 'impossible de récupérer l\'utilisateur'], 400);
            }
            switch ($adherent['ct']) {
                case 2 : $ct = '>25 ans'; $tarif_id = 8; break;
                case 3 : $ct = '18 - 25 ans'; $tarif_id = 9; break;
                case 4 : $ct = '<18 ans'; $tarif_id = 10; break;
                case 5 : $ct = 'Famille'; $tarif_id = 11; break;
                case 6 : $ct = 'Second club'; $tarif_id = 12; break;
                default : $ct = ''; $tarif_id = ''; break;
            }
            $tarif = Tarif::where('id', $tarif_id)->where('statut', 0)->first();
            $line = ['prenom' => $utilisateur->personne->prenom, 'nom' => $utilisateur->personne->nom, 'identifiant' => $utilisateur->identifiant,
                'ct' => $ct, 'tarif' => $tarif->tarif];

            $tab_adherents[$utilisateur->id]['adhesion'] = $line;
        }
        $tarif_abonne = Tarif::where('id', 17)->where('statut', 0)->first();
        foreach ($request->abonnes as $abonne) {
            $utilisateur = Utilisateur::where('id', $abonne)->first();
            if (!$utilisateur) {
                return new JsonResponse(['erreur' => 'impossible de récupérer l\'utilisateur'], 400);
            }
            $tab_adherents[$utilisateur->id]['abonnement'] = $tarif_abonne->tarif;
        }
        dd($tab_adherents);
    }
}
