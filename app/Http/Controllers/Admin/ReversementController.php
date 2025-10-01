<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Reglement;
use App\Models\Reversement;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReversementController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index() {
        if (!$this->checkDroit('GESTRE')) {
            return redirect()->route('accueil');
        }
        return view('admin.reversements.index');
    }

    public function attente() {
        if (!$this->checkDroit('GESTRE')) {
            return redirect()->route('accueil');
        }
        // on récupères tous les règlements pour lesquels il n'y a pas de reversement
        $tab_reversements = [];
        $config = Configsaison::where('id', 1)->selectRaw('tauxreversadh, tauxreversclub, tauxreversabt')->first();
        $tauxreversadh = round($config->tauxreversadh / 100, 2);
        $tauxreversclub = round($config->tauxreversclub / 100, 2);
        $tauxreversabt = round($config->tauxreversabt / 100, 2);
        $tarif_abo_club = Tarif::where('id', 5)->where('statut', 0)->first();
        $montant_reversement_abo_club = round($tarif_abo_club->tarif * $tauxreversabt, 2);
        $tarif_abo_adherent = Tarif::where('id', 17)->where('statut', 0)->first();
        $montant_reversement_abo_adherent =  round($tarif_abo_adherent->tarif * $tauxreversabt, 2);
        $tarif_adhesion_club_normal = Tarif::where('id', 6)->where('statut', 0)->first();
        $montant_reversement_adhesion_club_normal = round($tarif_adhesion_club_normal->tarif * $tauxreversclub, 2);
        $montant_reversement_adhesion_club_second_year = $montant_reversement_adhesion_club_normal / 2;
        $tarif_adhesion_club_nouveau = Tarif::where('id', 7)->where('statut', 0)->first();
        $montant_reversement_adhesion_club_nouveau = round($tarif_adhesion_club_nouveau->tarif * $tauxreversclub, 2);

        $tarif_adhesion_ct_2 = Tarif::where('id', 8)->where('statut', 0)->first();
        $tarif_adhesion_ct_3 = Tarif::where('id', 9)->where('statut', 0)->first();
        $tarif_adhesion_ct_4 = Tarif::where('id', 10)->where('statut', 0)->first();
        $tarif_adhesion_ct_5 = Tarif::where('id', 11)->where('statut', 0)->first();
        $tarif_adhesion_ct_6 = Tarif::where('id', 12)->where('statut', 0)->first();
        $tarif_adhesion_ct_7 = Tarif::where('id', 13)->where('statut', 0)->first();
        $tarif_adhesion_ct_8 = Tarif::where('id', 14)->where('statut', 0)->first();
        $tarif_adhesion_ct_9 = Tarif::where('id', 15)->where('statut', 0)->first();
        $tarif_adhesion_ct_F = Tarif::where('id', 16)->where('statut', 0)->first();
        $montant_reversement_adhesion_ct_2 = round($tarif_adhesion_ct_2->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_3 = round($tarif_adhesion_ct_3->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_4 = round($tarif_adhesion_ct_4->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_5 = round($tarif_adhesion_ct_5->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_6 = round($tarif_adhesion_ct_6->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_7 = round($tarif_adhesion_ct_7->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_8 = round($tarif_adhesion_ct_8->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_9 = round($tarif_adhesion_ct_9->tarif * $tauxreversadh, 2);
        $montant_reversement_adhesion_ct_F = round($tarif_adhesion_ct_F->tarif * $tauxreversadh, 2);

        $reglements = Reglement::whereNull('reversement_id')->where('statut', 1)->orderByDesc('id')->get();
        foreach($reglements as $reglement) {
            if ($reglement->clubs_id) {
                // ca concerne des clubs
                $club = Club::where('id', $reglement->clubs_id)->first();
                if ($club) {
                    // on enregitre dans la table de l'ur du club
                    if ($reglement->aboClub == 1) {
                        // on ajoute le montant de l'abonnement cubs
                        if (!isset($tab_reversements[$club->urs_id][$reglement->clubs_id]['abonnements'])) {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['abonnements'] = $montant_reversement_abo_club;
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['nom'] = $club->nom;
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['numero'] = $club->numero;
                        } else {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['abonnements'] += $montant_reversement_abo_club;
                        }
                        if (!isset($tab_reversements[$club->urs_id][$reglement->clubs_id]['total'])) {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['total'] = $montant_reversement_abo_club;
                        } else {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['total'] += $montant_reversement_abo_club;
                        }
                        if (!isset($tab_reversements[$club->urs_id]['total']['abonnements'])) {
                            $tab_reversements[$club->urs_id]['total']['abonnements'] = $montant_reversement_abo_club;
                        } else {
                            $tab_reversements[$club->urs_id]['total']['abonnements'] += $montant_reversement_abo_club;
                        }
                        if (!isset($tab_reversements[$club->urs_id]['total']['total'])) {
                            $tab_reversements[$club->urs_id]['total']['total'] = $montant_reversement_abo_club;
                        } else {
                            $tab_reversements[$club->urs_id]['total']['total'] += $montant_reversement_abo_club;
                        }

                    }
                    if ($reglement->adhClub == 1) {
                        $montant_adhesion_ur = $club->ct == 'N' ?  $montant_reversement_adhesion_club_nouveau : $montant_reversement_adhesion_club_normal;
                        if ($club->second_year == 1) {
                            $montant_adhesion_ur = $montant_reversement_adhesion_club_second_year;
                        }
                        // on ajoute le montant de l'adhésion club
                        if (!isset($tab_reversements[$club->urs_id][$reglement->clubs_id]['adhesion_ur'])) {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['adhesion_ur'] = $montant_adhesion_ur;
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['nom'] = $club->nom;
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['numero'] = $club->numero;
                        } else {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['adhesion_ur'] += $montant_adhesion_ur;
                        }
                        if (!isset($tab_reversements[$club->urs_id][$reglement->clubs_id]['total'])) {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['total'] = $montant_adhesion_ur;
                        } else {
                            $tab_reversements[$club->urs_id][$reglement->clubs_id]['total'] += $montant_adhesion_ur;
                        }
                        if (!isset($tab_reversements[$club->urs_id]['total']['adhesion_ur'])) {
                            $tab_reversements[$club->urs_id]['total']['adhesion_ur'] = $montant_adhesion_ur;
                        } else {
                            $tab_reversements[$club->urs_id]['total']['adhesion_ur'] += $montant_adhesion_ur;
                        }
                        if (!isset($tab_reversements[$club->urs_id]['total']['total'])) {
                            $tab_reversements[$club->urs_id]['total']['total'] = $montant_adhesion_ur;
                        } else {
                            $tab_reversements[$club->urs_id]['total']['total'] += $montant_adhesion_ur;
                        }
                    }
//                    if ($reglement->id == 40477) {
//                        dd($tab_reversements);
//                    }
                }
            }

            // on récupère tus les utilisateurs concernés par le règlement
            $utilisateurs = DB::table('reglementsutilisateurs')
                ->join('utilisateurs', 'utilisateurs.id', '=', 'reglementsutilisateurs.utilisateurs_id')
                ->where('reglementsutilisateurs.reglements_id', $reglement->id)
                ->selectRaw('reglementsutilisateurs.abonnement, reglementsutilisateurs.adhesion, utilisateurs.ct, utilisateurs.urs_id, utilisateurs.clubs_id')
                ->get();
            foreach ($utilisateurs as $utilisateur) {
                if (!$utilisateur->clubs_id) {
                    $utilisateur->clubs_id = 0;
                }
                if ($utilisateur->abonnement && $utilisateur->clubs_id != 0) {
                    $club = Club::where('id', $utilisateur->clubs_id)->first();
                    // on ajoute le montant de l'abonnement
                    if (!isset($tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['abonnements'])) {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['abonnements'] = $montant_reversement_abo_adherent;
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['nom'] = $club->nom;
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['numero'] = $club->numero;
                    } else {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['abonnements'] += $montant_reversement_abo_adherent;
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['total'])) {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['total'] = $montant_reversement_abo_adherent;
                    } else {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['total'] += $montant_reversement_abo_adherent;
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id]['total']['abonnements'])) {
                        $tab_reversements[$utilisateur->urs_id]['total']['abonnements'] = $montant_reversement_abo_adherent;
                    } else {
                        $tab_reversements[$utilisateur->urs_id]['total']['abonnements'] += $montant_reversement_abo_adherent;
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id]['total']['total'])) {
                        $tab_reversements[$utilisateur->urs_id]['total']['total'] = $montant_reversement_abo_adherent;
                    } else {
                        $tab_reversements[$utilisateur->urs_id]['total']['total'] += $montant_reversement_abo_adherent;
                    }
                }
                if ($utilisateur->adhesion) {
                    $montant_adhesion_adherent = match ($utilisateur->ct) {
                        '2', 2 => $montant_reversement_adhesion_ct_2,
                        '3', 3 => $montant_reversement_adhesion_ct_3,
                        '4', 4 => $montant_reversement_adhesion_ct_4,
                        '5', 5 => $montant_reversement_adhesion_ct_5,
                        '6', 6 => $montant_reversement_adhesion_ct_6,
                        '7', 7 => $montant_reversement_adhesion_ct_7,
                        '8', 8 => $montant_reversement_adhesion_ct_8,
                        '9', 9 => $montant_reversement_adhesion_ct_9,
                        'F' => $montant_reversement_adhesion_ct_F,
                        default => 0,
                    };
                    if ($utilisateur->clubs_id != 0) {
                        $club = Club::where('id', $utilisateur->clubs_id)->first();
                        $nom_club = $club->nom;
                        $numero_club = $club->numero;
                    } else {
                        $nom_club = 'Individuels';
                        $numero_club = '';
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['cartes'])) {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['cartes'] = $montant_adhesion_adherent;
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['nom'] = $nom_club;
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['numero'] = $numero_club;
                    } else {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['cartes'] += $montant_adhesion_adherent;
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['total'])) {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['total'] = $montant_adhesion_adherent;
                    } else {
                        $tab_reversements[$utilisateur->urs_id][$utilisateur->clubs_id]['total'] += $montant_adhesion_adherent;
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id]['total']['cartes'])) {
                        $tab_reversements[$utilisateur->urs_id]['total']['cartes'] = $montant_adhesion_adherent;
                    } else {
                        $tab_reversements[$utilisateur->urs_id]['total']['cartes'] += $montant_adhesion_adherent;
                    }
                    if (!isset($tab_reversements[$utilisateur->urs_id]['total']['total'])) {
                        $tab_reversements[$utilisateur->urs_id]['total']['total'] = $montant_adhesion_adherent;
                    } else {
                        $tab_reversements[$utilisateur->urs_id]['total']['total'] += $montant_adhesion_adherent;
                    }

                }
            }
        }
        ksort($tab_reversements);
//        foreach ($tab_reversements as $k => $reversement) {
//            foreach($reversement as $j => $v) {
//                if($j != 'total') {
//                    dd($v);
//                }
//            }
//        }
//        dd($tab_reversements);

        return view('admin.reversements.attente', compact('tab_reversements'));
    }

    public function effectues() {
        if (!$this->checkDroit('GESTRE')) {
            return redirect()->route('accueil');
        }
        $reversements = Reversement::orderByDesc('id')->paginate(100);
        foreach ($reversements as $reversement) {
            list($annee,$ur, $tmp) = explode('-', $reversement->reference);
            $reversement->bordereau = url('storage/app/public/uploads/bordereauxur/20'.$annee).'/bordereau-ur-'.$reversement->reference.'.pdf';
        }
        return view('admin.reversements.effectues', compact('reversements'));
    }
}
