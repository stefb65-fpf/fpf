<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ReglementController extends Controller
{
    public function validReglement(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->where('statut', 0)->first();
        if(!$reglement) {
            return new JsonResponse(['erreur' => 'règlement introubale'], 400);
        }
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
        $numeroencours = $config->numeroencours;

        // on traite tous les utilisateurs en passant leur statut à 2 et / ou en prologeant ou créant leur abonnement
        $utilisateurs = Utilisateur::join('reglementsutilisateurs', 'utilisateurs.id', '=', 'reglementsutilisateurs.utilisateurs_id')
            ->where('reglementsutilisateurs.reglements_id', $reglement->id)
            ->get();
        foreach ($utilisateurs as $utilisateur) {
            $datap = array(); // données à mettre à jour sur la personne
            $datau = array(); // données à mettre à jour sur l'utilisateur
            if ($utilisateur->adhesion == 1) {
                $datau = array('statut' => 2, 'saison' => date('Y'));
                $datap['is_adherent'] = 1;
                $utilisateur->update($datau);
            }
            if ($utilisateur->abonnement == 1) {
                $datap['is_abonne'] = 1;

                // on regarde si l'utilisateur a déjà un abonnement en cours
                $abonnement = Abonnement::where('personne_id', $utilisateur->personne_id)->where('etat', 1)->first();
                if ($abonnement) {
                    // on crée un abonnement avec état 0
                    $debut = $abonnement->fin + 1;
                    $fin = $abonnement->fin + 5;
                    $dataa = array('personne_id' => $utilisateur->personne_id, 'etat' => 0, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                } else {
                    // on crée un abonnement avec état 1
                    $debut = $numeroencours;
                    $fin = $numeroencours + 4;
                    $dataa = array('personne_id' => $utilisateur->personne_id, 'etat' => 1, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                }
                Abonnement::create($dataa);
            }
            $personne = Personne::where('id', $utilisateur->personne_id)->first();
            $personne->update($datap);
        }

        // on met à jour le club si besoin
        if ($reglement->aboClub == 1 || $reglement->adhClub == 1) {
            $club = Club::where('id', $reglement->clubs_id)->first();
            $datac = array('statut' => 2);
            if ($club->ct == 'N') {
                $datac['ct'] = '1';
                $datac['second_year'] = 1;
            }
            if ($club->second_year == 1) {
                $datac['second_year'] = 0;
            }
            if ($reglement->aboClub == 1) {
                if ($numeroencours > $club->numerofinabonnement) {
                    $datac['numerofinabonnement'] = $numeroencours + 5;
                } else {
                    $datac['numerofinabonnement'] = $club->numerofinabonnement + 5;
                }
            }
            $club->update($datac);
        }

        // on met à jour le règlement
        $datar = array('statut' => 1, 'numerocheque' => $request->infos, 'dateenregistrement' => date('Y-m-d H:i:s'));
        $reglement->update($datar);

        return new JsonResponse(['success' => "Le règlement a été validé"], 200);
    }

    public function editCartes() {
        $utilisateurs = Utilisateur::where('statut', 2)->whereNotNull('personne_id')->where('urs_id', '<>', 0)->orderBy('clubs_id')->get();
        $tab_cartes = []; $table_vignettes = [];
        $tab_individuels = []; $tab_clubs = [];
        foreach ($utilisateurs as $utilisateur) {
            if (in_array($utilisateur->nb_cases_carte, [0,3])) {
                $tab_cartes[] = $utilisateur;
                $utilisateur->type_edition = 'carte';
            } else {
                $table_vignettes[] = $utilisateur;
                $utilisateur->type_edition = 'vignette';
            }
            if ($utilisateur->clubs_id == null) {
                // individuel
                $tab_individuels[] = $utilisateur;
            } else {
                // club
                if ($utilisateur->type_edition == 'carte') {
                    $tab_clubs[$utilisateur->clubs_id]['cartes'][] = $utilisateur;
                } else {
                    $tab_clubs[$utilisateur->clubs_id]['vignettes'][] = $utilisateur;
                }

            }
        }
        foreach ($tab_clubs as $k => $tab_club) {
            // on recherche le contact du club
            $club = Club::where('id', $k)->selectRaw('nom, numero')->first();
            $tab_clubs[$k]['club'] = $club;

            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $k)
                ->whereNotNull('utilisateurs.personne_id')
                ->first();
            if ($contact) {
                $tab_clubs[$k]['contact'] = $contact;
            }
        }

        $dir = storage_path().'/app/public/cartes/'.date('Y').'/'.date('m');
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file_cartes = '';
        $rand = rand(100000, 999999);
        $name = 'cartes_'.$rand.'_'.date('YmdHis').'.pdf';
        if (sizeof($tab_cartes) > 0) {
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('pdf.cartes', compact('tab_cartes'))
                ->setWarnings(false)
                ->setPaper([0.0, 0.0, 153, 241], 'landscape')
                ->save($dir.'/'.$name);
            list($tmp, $file_cartes) = explode('htdocs/', $dir.'/'.$name);
        }

        $file_etiquettes_individuels = '';
        if (sizeof($tab_individuels) > 0) {
            // on imprime un fichier d'étiquettes carte
            $name = 'etiquettes_renouvellement_individuels_'.$rand.'_'.date('YmdHis').'.pdf';
            if (sizeof($tab_cartes) > 0) {
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('pdf.etiquettes_renouvellement_individuels', compact('tab_individuels'))
                    ->setWarnings(false)
                    ->setPaper('a4', 'portrait')
                    ->save($dir.'/'.$name);
                list($tmp, $file_etiquettes_individuels) = explode('htdocs/', $dir.'/'.$name);
            }
        }

        $file_etiquettes_club = '';
        if (sizeof($tab_clubs) > 0) {
            // on imprime un fichier de livraison par club
            $name = 'etiquettes_renouvellement_clubs_'.$rand.'_'.date('YmdHis').'.pdf';
            if (sizeof($tab_cartes) > 0) {
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('pdf.etiquettes_renouvellement_clubs', compact('tab_clubs'))
                    ->setWarnings(false)
                    ->setPaper('a4', 'portrait')
                    ->save($dir.'/'.$name);

                list($tmp, $file_etiquettes_club) = explode('htdocs/', $dir.'/'.$name);
            }
        }

        // une fois les cartes éditées, on met à jour les utilisateurs
        foreach ($utilisateurs as $utilisateur) {
            unset($utilisateur->type_edition);
            $last_saison_case = (in_array(date('m'), ['09', '10', '11', '12'])) ? date('Y') : date('Y') - 1;
            $new_nb_cases_carte = in_array($utilisateur->nb_cases_carte, [0, 3]) ? 1 : $utilisateur->nb_cases_carte + 1;
            $datau = array('statut' => 3, 'saison' => date('Y'), 'last_saison_case' => $last_saison_case,
                'nb_cases_carte' => $new_nb_cases_carte);
            $utilisateur->update($datau);
        }

        return new JsonResponse(['file_cartes' => $file_cartes, 'file_etiquettes_club' => $file_etiquettes_club, 'file_etiquettes_individuels' => $file_etiquettes_individuels], 200);
    }
}
