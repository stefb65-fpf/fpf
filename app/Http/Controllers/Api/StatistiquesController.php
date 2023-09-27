<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatistiquesController extends Controller
{
    public function gestStatsClub(Request $request) {
        if ($request->level == 'fpf') {
            // on cherche le nombre de clubs renouvelés
            $nb_non_renouveles = Club::whereIn('statut', [0,1])->count();
            $nb_valides = Club::where('statut', 2)->where('second_year', 0)->count();
            $nb_preinscrits = Club::where('statut', 1)->where('second_year', 0)->count();
            $nb_nouveaux = Club::where('statut', 2)->where('second_year', 1)->count();
            $tab = array('non_renouveles' => $nb_non_renouveles, 'valides' => $nb_valides, 'nouveaux' => $nb_nouveaux, 'preinscrits' => $nb_preinscrits);
            return new JsonResponse($tab, 200);
        }
        if ($request->level == 'ur') {
            // on cherche le nombre de clubs renouvelés
            $nb_non_renouveles = Club::whereIn('statut', [0,1])->where('urs_id', $request->ur_id)->count();
            $nb_valides = Club::where('statut', 2)->where('second_year', 0)->where('urs_id', $request->ur_id)->count();
            $nb_preinscrits = Club::where('statut', 1)->where('second_year', 0)->count();
            $nb_nouveaux = Club::where('statut', 2)->where('second_year', 1)->where('urs_id', $request->ur_id)->count();
            $tab = array('non_renouveles' => $nb_non_renouveles, 'valides' => $nb_valides, 'nouveaux' => $nb_nouveaux, 'preinscrits' => $nb_preinscrits);
            return new JsonResponse($tab, 200);
        }
    }

    public function gestStatsAdherents(Request $request) {
        if (in_array(date('m'), [9,10,11,12])) {
            $date_debut_saison = date('Y') . '-09-01';
        } else {
            $date_debut_saison = (date('Y') - 1) . '-09-01';
        }
        if ($request->level == 'fpf') {
            $nb_renouveles = Utilisateur::whereIn('statut', [2,3])->where(
                function ($query) use ($date_debut_saison) {
                    $query->where('created_at', '<', $date_debut_saison)
                        ->orWhereNull('created_at');
                })->count();
            $nb_nouveaux = Utilisateur::whereIn('statut', [2,3])->where('created_at', '>=', $date_debut_saison)->count();
            $nb_preinscrits = Utilisateur::where('statut', 1)->count();
            $nb_nonrenouveles = Utilisateur::where('statut', 0)->count();

            $tab = array('non_renouveles' => $nb_nonrenouveles, 'valides' => $nb_renouveles, 'nouveaux' => $nb_nouveaux, 'preinscrits' => $nb_preinscrits);
            return new JsonResponse($tab, 200);
        }
        if ($request->level == 'ur') {
            $nb_renouveles = Utilisateur::whereIn('statut', [2,3])->where('urs_id', $request->ur_id)->where(
                function ($query) use ($date_debut_saison) {
                    $query->where('created_at', '<', $date_debut_saison)
                        ->orWhereNull('created_at');
                })->count();
            $nb_nouveaux = Utilisateur::whereIn('statut', [2,3])->where('urs_id', $request->ur_id)->where('created_at', '>=', $date_debut_saison)->count();
            $nb_preinscrits = Utilisateur::where('statut', 1)->where('urs_id', $request->ur_id)->count();
            $nb_nonrenouveles = Utilisateur::where('statut', 0)->where('urs_id', $request->ur_id)->count();

            $tab = array('non_renouveles' => $nb_nonrenouveles, 'valides' => $nb_renouveles, 'nouveaux' => $nb_nouveaux, 'preinscrits' => $nb_preinscrits);
            return new JsonResponse($tab, 200);
        }
    }

    public function gestStatsRepartitionCartes(Request $request) {
        if ($request->level == 'fpf') {
            $nb_ct2 = Utilisateur::where('ct', 2)->whereIn('statut', [2,3])->count();
            $nb_ct3 = Utilisateur::where('ct', 3)->whereIn('statut', [2,3])->count();
            $nb_ct4 = Utilisateur::where('ct', 4)->whereIn('statut', [2,3])->count();
            $nb_ct5 = Utilisateur::where('ct', 5)->whereIn('statut', [2,3])->count();
            $nb_ct6 = Utilisateur::where('ct', 6)->whereIn('statut', [2,3])->count();
            $nb_ct7 = Utilisateur::where('ct', 7)->whereIn('statut', [2,3])->count();
            $nb_ct8 = Utilisateur::where('ct', 8)->whereIn('statut', [2,3])->count();
            $nb_ct9 = Utilisateur::where('ct', 9)->whereIn('statut', [2,3])->count();
            $nb_ctf = Utilisateur::where('ct', 'F')->whereIn('statut', [2,3])->count();
            $tab = array('ct2' => $nb_ct2, 'ct3' => $nb_ct3, 'ct4' => $nb_ct4, 'ct5' => $nb_ct5, 'ct6' => $nb_ct6, 'ct7' => $nb_ct7, 'ct8' => $nb_ct8, 'ct9' => $nb_ct9, 'ctf' => $nb_ctf);
            return new JsonResponse($tab, 200);
        }
        if ($request->level == 'ur') {
            $nb_ct2 = Utilisateur::where('ct', 2)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct3 = Utilisateur::where('ct', 3)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct4 = Utilisateur::where('ct', 4)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct5 = Utilisateur::where('ct', 5)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct6 = Utilisateur::where('ct', 6)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct7 = Utilisateur::where('ct', 7)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct8 = Utilisateur::where('ct', 8)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ct9 = Utilisateur::where('ct', 9)->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $nb_ctf = Utilisateur::where('ct', 'F')->where('urs_id', $request->ur_id)->whereIn('statut', [2,3])->count();
            $tab = array('ct2' => $nb_ct2, 'ct3' => $nb_ct3, 'ct4' => $nb_ct4, 'ct5' => $nb_ct5, 'ct6' => $nb_ct6, 'ct7' => $nb_ct7, 'ct8' => $nb_ct8, 'ct9' => $nb_ct9, 'ctf' => $nb_ctf);
            return new JsonResponse($tab, 200);
        }
    }
}
