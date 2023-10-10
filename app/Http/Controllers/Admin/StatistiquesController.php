<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Souscription;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
{
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
}
