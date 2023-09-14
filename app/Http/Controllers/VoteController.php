<?php

namespace App\Http\Controllers;

use App\Models\Personne;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkLogin', 'userAccessOnly']);
    }

    public function index(Vote $vote) {
        $cartes = session()->get('cartes');
        $personne = session()->get('user');
        if (!isset($cartes[0])) {
            return redirect()->route('accueil');
        }
        // on regarde si l'utilisateur a déjà voté
        $vote_utilisateur = DB::table('votes_utilisateurs')
            ->where('utilisateurs_id', $cartes[0]->id)
            ->where('votes_id', $vote->id)
            ->where('statut', 1)
            ->first();
        if ($vote_utilisateur) {
            return redirect()->route('accueil');
        }

        $vote_club = 0;
        if ($vote->vote_club == 1) {
            // on regarde si le membre a un droit de vote club
            $fonction = DB::table('fonctionsutilisateurs')
                ->where('utilisateurs_id', $cartes[0]->id)
                ->whereIn('fonctions_id', [94, 97])
                ->first();
            if ($fonction) {
                // l'adhérent est contact ou président de club, on regarde si le vote club a déjà été effectué
                $vote_club = DB::table('votes_clubs')
                    ->where('votes_id', $vote->id)
                    ->where('clubs_id', $cartes[0]->clubs_id)
                    ->first();
                if (!$vote_club) {
                    $vote_club = 1;
                }
            }
        }
        $nb_voix = 0;
        if ($vote->type == 1) {
            if ($vote->phase == 2) {
                // on regarde si le membre a un droit de vote club
                $fonction = DB::table('fonctionsutilisateurs')
                    ->where('utilisateurs_id', $cartes[0]->id)
                    ->whereIn('fonctions_id', [94, 97])
                    ->first();
                if ($fonction) {
                    // on va récupérer le nombre de voix club si celles-ci n'ont pas été utilisées
                    $voteclub = DB::table('cumul_votes_clubs')
                        ->where('clubs_id', $cartes[0]->clubs_id)
                        ->where('statut', 0)
                        ->where('votes_id', $vote->id)
                        ->first();
                    if ($voteclub) {
                        $nb_voix = $voteclub->nb_voix;
                    }
                }
            }

            if ($vote->phase == 3) {
                // on regarde si le membre a un droit de vote UR
                $fonction = DB::table('fonctionsutilisateurs')
                    ->where('utilisateurs_id', $cartes[0]->id)
                    ->where('fonctions_id', 57)
                    ->first();
                if ($fonction) {
                    // on va récupérer le nombre de voix UR si celles-ci n'ont pas été utilisées
                    $voteclub = DB::table('cumul_votes_urs')
                        ->where('urs_id', $cartes[0]->urs_id)
                        ->where('statut', 0)
                        ->where('votes_id', $vote->id)
                        ->first();
                    if ($voteclub) {
                        $nb_voix = $voteclub->nb_voix;
                    }
                }
            }
        }

        $elections = $vote->elections->sortBy('ordre');
        foreach ($elections as $election) {
            if ($election->type == 2) {
                $election->candidats = $election->candidats->sortBy('ordre');
                foreach ($election->candidats as $k => $candidat) {
                    $lapersonne = Personne::where('id', $candidat->utilisateur->personne_id)->selectRaw('nom,prenom')->first();
                    if ($lapersonne) {
                        $election->candidats[$k]->personne = $lapersonne;
                    } else {
                        unset($election->candidats[$k]);
                    }
                }
            }
        }

        return view('votes.index', compact('vote', 'elections', 'vote_club', 'nb_voix', 'personne'));
    }
}
