<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatistiquesController extends Controller
{
    public function gestStatsClub(Request $request) {
        if ($request->level == 'fpf') {
            // on cherche le nombre de clubs renouvelés
            $nb_non_renouveles = Club::where('statut', 0)->count();
            $nb_valides = Club::where('statut', 2)->where('second_year', 0)->count();
            $nb_preinscrits = Club::where('statut', 1)->where('second_year', 0)->count();
            $nb_nouveaux = Club::where('statut', 2)->where('second_year', 1)->count();
            $tab = array('non_renouveles' => $nb_non_renouveles, 'valides' => $nb_valides, 'nouveaux' => $nb_nouveaux, 'preinscrits' => $nb_preinscrits);
            return new JsonResponse($tab, 200);
        }
        if ($request->level == 'ur') {
            // on cherche le nombre de clubs renouvelés
            $nb_non_renouveles = Club::where('statut', 0)->where('urs_id', $request->ur_id)->count();
            $nb_valides = Club::where('statut', 2)->where('second_year', 0)->where('urs_id', $request->ur_id)->count();
            $nb_preinscrits = Club::where('statut', 1)->where('second_year', 0)->where('urs_id', $request->ur_id)->count();
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
//            $nb_renouveles = Utilisateur::whereIn('statut', [2,3])->where(
//                function ($query) use ($date_debut_saison) {
//                    $query->where('created_at', '<', $date_debut_saison)
//                        ->orWhereNull('created_at');
//                })->count();
            $nb_renouveles = Utilisateur::join('utilisateurs_prec', 'utilisateurs_prec.id', '=', 'utilisateurs.id')
                ->whereIn('utilisateurs.statut', [2,3])
                ->count('utilisateurs.id');
            $nb_nouveaux = Utilisateur::whereIn('statut', [2,3])->where('created_at', '>=', $date_debut_saison)->count();
            $nb_preinscrits = Utilisateur::where('statut', 1)->count();
            // SELECT COUNT(U.id) FROM utilisateurs_prec P, utilisateurs U WHERE U.id = P.id AND U.statut = 0 AND P.statut IN (2,3)
            $nb_nonrenouveles = Utilisateur::join('utilisateurs_prec', 'utilisateurs_prec.id', '=', 'utilisateurs.id')
                ->where('utilisateurs.statut', 0)
                ->whereIn('utilisateurs_prec.statut', [2,3])
                ->count('utilisateurs.id');

            $tab = array('non_renouveles' => $nb_nonrenouveles, 'valides' => $nb_renouveles, 'nouveaux' => $nb_nouveaux, 'preinscrits' => $nb_preinscrits);
            return new JsonResponse($tab, 200);
        }
        if ($request->level == 'ur') {
//            $nb_renouveles = Utilisateur::whereIn('statut', [2,3])->where('urs_id', $request->ur_id)->where(
//                function ($query) use ($date_debut_saison) {
//                    $query->where('created_at', '<', $date_debut_saison)
//                        ->orWhereNull('created_at');
//                })->count();
            $nb_renouveles = Utilisateur::join('utilisateurs_prec', 'utilisateurs_prec.id', '=', 'utilisateurs.id')
                ->whereIn('utilisateurs.statut', [2,3])
                ->where('utilisateurs.urs_id', $request->ur_id)
                ->count('utilisateurs.id');
            $nb_nouveaux = Utilisateur::whereIn('statut', [2,3])->where('urs_id', $request->ur_id)->where('created_at', '>=', $date_debut_saison)->count();
            $nb_preinscrits = Utilisateur::where('statut', 1)->where('urs_id', $request->ur_id)->count();
            $nb_nonrenouveles = Utilisateur::where('statut', 0)->where('urs_id', $request->ur_id)->count();
            $nb_nonrenouveles = Utilisateur::join('utilisateurs_prec', 'utilisateurs_prec.id', '=', 'utilisateurs.id')
                ->where('utilisateurs.statut', 0)
                ->where('utilisateurs.urs_id', $request->ur_id)
                ->whereIn('utilisateurs_prec.statut', [2,3])
                ->count('utilisateurs.id');

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

    public function gestStatsEvolution(Request $request) {
        $debut_saison_actuelle = Configsaison::where('id', 1)->first()->datedebut;
        $fin_saison_actuelle = Configsaison::where('id', 1)->first()->datefin;
        $debut_saison_prev = Configsaison::where('id', 0)->first()->datedebut;
        $fin_saison_prev = Configsaison::where('id', 0)->first()->datefin;
        $saison_actuelle = intval(substr($debut_saison_actuelle, 0, 4));
        $saison_future = intval(substr($debut_saison_prev, 0, 4));
        $saison_actuelle_str = $saison_actuelle . '-' . ($saison_actuelle + 1);
        $saison_future_str = $saison_future . '-' . ($saison_future + 1);
        $query_actuels = Reglement::join('reglementsutilisateurs', 'reglements.id', '=', 'reglementsutilisateurs.reglements_id')
            ->where('reglements.statut', 1)
            ->where('reglements.dateenregistrement', '>=', $debut_saison_actuelle)
            ->where('reglements.dateenregistrement', '<=', $fin_saison_actuelle)
            ->where('reglementsutilisateurs.adhesion', 1)
            ->selectRaw('COUNT(reglementsutilisateurs.utilisateurs_id) as nb, reglements.dateenregistrement, reglements.id, reglements.adhClub')
            ->orderBy('reglements.dateenregistrement', 'asc')
            ->groupBy('reglements.id');

        if ($request->level == 'ur') {
            $query_actuels->join('utilisateurs', 'reglementsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.urs_id', $request->ur_id);
        }
        $reglements_actuels = $query_actuels->get();

        $query_prev = Reglement::join('reglementsutilisateurs', 'reglements.id', '=', 'reglementsutilisateurs.reglements_id')
            ->where('reglements.statut', 1)
            ->where('reglements.dateenregistrement', '>=', $debut_saison_prev)
            ->where('reglements.dateenregistrement', '<=', $fin_saison_prev)
            ->where('reglementsutilisateurs.adhesion', 1)
            ->selectRaw('COUNT(reglementsutilisateurs.utilisateurs_id) as nb, reglements.dateenregistrement, reglements.id, reglements.adhClub')
            ->orderBy('reglements.dateenregistrement', 'asc')
            ->groupBy('reglements.id');

        if ($request->level == 'ur') {
            $query_prev->join('utilisateurs', 'reglementsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.urs_id', $request->ur_id);
        }

        $reglements_prev = $query_prev->get();

        $tab_adhesion = array();
        foreach ($reglements_actuels as $reglement) {
            $mois = intval(substr($reglement->dateenregistrement, 3, 2));
            if (isset($tab_adhesion[0][$mois])) {
                $tab_adhesion[0][$mois]['nb'] += $reglement->nb;
                if ($reglement->adhClub == 1) {
                    if (isset($tab_adhesion[0][$mois]['club'])) {
                        $tab_adhesion[0][$mois]['club'] += 1;
                    } else {
                        $tab_adhesion[0][$mois]['club'] = 1;
                    }
                }
            } else {
                $tab_adhesion[0][$mois]['nb'] = $reglement->nb;
                if ($reglement->adhClub == 1) {
                    $tab_adhesion[0][$mois]['club'] = 1;
                }
            }
        }

        foreach ($reglements_prev as $reglement) {
            $mois = intval(substr($reglement->dateenregistrement, 3, 2));
            if (isset($tab_adhesion[1][$mois])) {
                $tab_adhesion[1][$mois]['nb'] += $reglement->nb;
                if ($reglement->adhClub == 1) {
                    if (isset($tab_adhesion[1][$mois]['club'])) {
                        $tab_adhesion[1][$mois]['club'] += 1;
                    } else {
                        $tab_adhesion[1][$mois]['club'] = 1;
                    }
                }
            } else {
                $tab_adhesion[1][$mois]['nb'] = $reglement->nb;
                if ($reglement->adhClub == 1) {
                    $tab_adhesion[1][$mois]['club'] = 1;
                }
            }
        }

        // on parcourt tab_adhesion dans l'ordre et on cumule
        $cumul = 0;
        $cumul_club = 0;
        $tab_clubs = [];
        foreach ($tab_adhesion[0] as $k => $v) {
            $cumul += $v['nb'];
            $cumul_club += $v['club']??0;
            $tab_adhesion[0][$k] = $cumul;
            $tab_clubs[0][$k] = $cumul_club;
        }

        // on parcourt tab_adhesion dans l'ordre et on cumule
        $cumul = 0;
        $cumul_club = 0;
        foreach ($tab_adhesion[1] as $k => $v) {
            $cumul += $v['nb'];
            $cumul_club += $v['club']??0;
            $tab_adhesion[1][$k] = $cumul;
            $tab_clubs[1][$k] = $cumul_club;
        }
        return new JsonResponse(['adhesions' => $tab_adhesion, 'clubs' => $tab_clubs, 'current_year' => $saison_actuelle_str, 'prev_year' => $saison_future_str], 200);
    }

}
