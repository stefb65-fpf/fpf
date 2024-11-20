<?php

namespace App\Http\Controllers\Admin;

use App\Concern\VoteTools;
use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Reglement;
use App\Models\Souscription;
use App\Models\Ur;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
{
    use VoteTools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index() {
        $nb_adherents = Utilisateur::whereIn('statut', [2,3])->count();
        $nb_adherents_previous = DB::table('utilisateurs_prec')->whereIn('statut', [2,3])->count();
        $ratio_adherents = round(($nb_adherents - $nb_adherents_previous) * 100 / $nb_adherents_previous, 2);
        $nb_clubs = Club::where('statut', 2)->count();
        $nb_clubs_previous = DB::table('clubs_prec')->where('statut', 2)->count();
        $ratio_clubs = round(($nb_clubs - $nb_clubs_previous) * 100 / $nb_clubs_previous, 2);
        $nb_abonnements = Abonnement::where('etat', 1)->count();
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        $nb_abonnements_clubs = Club::where('numerofinabonnement', '>=', $numeroencours)->count();
        $nb_souscriptions = Souscription::where('statut', 1)->sum('nbexemplaires');

//        $debut_saison_actuelle = Configsaison::where('id', 1)->first()->datedebut;
//        $fin_saison_actuelle = Configsaison::where('id', 1)->first()->datefin;
//        $debut_saison_prev = Configsaison::where('id', 0)->first()->datedebut;
//        $fin_saison_prev = Configsaison::where('id', 0)->first()->datefin;
//        $saison_actuelle = intval(substr($debut_saison_actuelle, 0, 4));
//        $saison_future = intval(substr($debut_saison_prev, 0, 4));
//
//        $query_actuels = Reglement::join('reglementsutilisateurs', 'reglements.id', '=', 'reglementsutilisateurs.reglements_id')
//            ->where('reglements.statut', 1)
//            ->where('reglements.dateenregistrement', '>=', $debut_saison_actuelle)
//            ->where('reglements.dateenregistrement', '<=', $fin_saison_actuelle)
//            ->where('reglementsutilisateurs.adhesion', 1)
//            ->selectRaw('COUNT(reglementsutilisateurs.utilisateurs_id) as nb, reglements.dateenregistrement, reglements.id, reglements.adhClub')
//            ->orderBy('reglements.dateenregistrement', 'asc')
//            ->groupBy('reglements.id');
//
//        $reglements_actuels = $query_actuels->get();
//
//        $query_prev = Reglement::join('reglementsutilisateurs', 'reglements.id', '=', 'reglementsutilisateurs.reglements_id')
//            ->where('reglements.statut', 1)
//            ->where('reglements.dateenregistrement', '>=', $debut_saison_prev)
//            ->where('reglements.dateenregistrement', '<=', $fin_saison_prev)
//            ->where('reglementsutilisateurs.adhesion', 1)
//            ->selectRaw('COUNT(reglementsutilisateurs.utilisateurs_id) as nb, reglements.dateenregistrement, reglements.id, reglements.adhClub')
//            ->orderBy('reglements.dateenregistrement', 'asc')
//            ->groupBy('reglements.id');
//        $reglements_prev = $query_prev->get();
//
//        $tab_adhesion = array();
//        foreach ($reglements_actuels as $reglement) {
//            $mois = intval(substr($reglement->dateenregistrement, 3, 2));
//            if (isset($tab_adhesion[0][$mois])) {
//                $tab_adhesion[0][$mois]['nb'] += $reglement->nb;
//                if ($reglement->adhClub == 1) {
//                    if (isset($tab_adhesion[0][$mois]['club'])) {
//                        $tab_adhesion[0][$mois]['club'] += 1;
//                    } else {
//                        $tab_adhesion[0][$mois]['club'] = 1;
//                    }
//                }
//            } else {
//                $tab_adhesion[0][$mois]['nb'] = $reglement->nb;
//                if ($reglement->adhClub == 1) {
//                    $tab_adhesion[0][$mois]['club'] = 1;
//                }
//            }
//        }
//
//        foreach ($reglements_prev as $reglement) {
//            $mois = intval(substr($reglement->dateenregistrement, 3, 2));
//            if (isset($tab_adhesion[1][$mois])) {
//                $tab_adhesion[1][$mois]['nb'] += $reglement->nb;
//                if ($reglement->adhClub == 1) {
//                    if (isset($tab_adhesion[1][$mois]['club'])) {
//                        $tab_adhesion[1][$mois]['club'] += 1;
//                    } else {
//                        $tab_adhesion[1][$mois]['club'] = 1;
//                    }
//                }
//            } else {
//                $tab_adhesion[1][$mois]['nb'] = $reglement->nb;
//                if ($reglement->adhClub == 1) {
//                    $tab_adhesion[1][$mois]['club'] = 1;
//                }
//            }
//        }
//        dd($tab_adhesion);

        // on regarde la répartition par cartes des adhésions actuelles
        $users = Utilisateur::whereIn('statut', [2,3])
            ->selectRaw('COUNT(id) as nb, ct, urs_id')
            ->whereIn('ct', [2,3,4,5,6,7,8,9,'F'])
            ->whereNotNull('urs_id')
            ->groupBy('urs_id')
            ->groupBy('ct')
            ->orderBy('urs_id')
            ->orderBy('ct')
            ->get();
        $preinscrits = Utilisateur::where('statut', 1)
            ->selectRaw('COUNT(id) as nb, urs_id')
            ->whereNotNull('urs_id')
            ->where('urs_id', '!=', 0)
            ->groupBy('urs_id')
            ->orderBy('urs_id')
            ->get();

        $tab_repartition = array();
        $tab_total = [
            'ct2'   => 0,
            'ct3'   => 0,
            'ct4'   => 0,
            'ct5'   => 0,
            'ct6'   => 0,
            'ct7'   => 0,
            'ct8'   => 0,
            'ct9'   => 0,
            'ctF'   => 0,
            'total' => 0,
            'preinscrits' => 0
        ];
        foreach($users as $user) {
            $tab_repartition[$user->urs_id]['ct'.$user->ct] = $user->nb;
            if (isset($tab_repartition[$user->urs_id]['total'])) {
                $tab_repartition[$user->urs_id]['total'] += $user->nb;
            } else {
                $tab_repartition[$user->urs_id]['total'] = $user->nb;
            }
            $tab_total['ct'.$user->ct] += $user->nb;
            $tab_total['total'] += $user->nb;
        }

        foreach ($preinscrits as $preinscrit) {
            $tab_repartition[$preinscrit->urs_id]['preinscrit'] = $preinscrit->nb;
            $tab_total['preinscrits'] += $preinscrit->nb;
        }

        return view('admin.statistiques.index',
            compact('nb_adherents', 'nb_adherents_previous', 'nb_clubs', 'nb_clubs_previous', 'nb_abonnements',
                'nb_abonnements_clubs', 'nb_souscriptions', 'ratio_adherents', 'ratio_clubs', 'tab_repartition', 'tab_total'));
    }

    public function statistiquesVotes() {
        // on prend les 20 derniers votes existants pour lesquels la date de début est passée
        $votes = Vote::where('debut', '<=', date('Y-m-d'))->where('urs_id', 0)->orderByDesc('id')->paginate(20);
        return view('admin.statistiques.votes', compact('votes'));
    }

    public function statistiquesVotesPhases(){
        list($vote, $details) = $this->getVoteDetail();
        $ur = '0';
        return view('admin.statistiques.votesphases', compact('details', 'vote', 'ur'));
    }

    public function statistiquesVoteDetail(Vote $vote, $ur_id) {
        $details = $this->getVoteDetailByUr($vote, $ur_id);
        $ur = Ur::where('id', $ur_id)->first();
//        dd($details);
        return view('admin.statistiques.votesdetail', compact('details', 'vote', 'ur'));
    }

    public function statistiquesListeVoteByClub(Vote $vote, $club_id, $ur_id) {
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return redirect()->route('admin.statistiques.votesphases')->with('error', 'Ce club n\'existe pas');
        }
        list($vote, $adherents) = $this->getNotVotedAdherents($club);
        return view('admin.statistiques.listevotesbyclub', compact('adherents', 'vote', 'club'));

    }
}
