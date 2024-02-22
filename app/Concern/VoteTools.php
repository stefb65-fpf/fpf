<?php

namespace App\Concern;

use App\Models\Club;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

trait VoteTools
{
    public function getVoteDetail() {
        $fin = date('Y-m-d', strtotime('-7 days'));
        $vote = Vote::where('type', 1)->where('phase', '>', 0)->where('urs_id', 0)->where('fin', '>=', $fin)->first();
        if (!$vote) {
            return [null, []];
        }
        $tab_votes = [];
        // on récupère le nombre d'adhérents par UR - nombre d'utilisateurs ayant le statut 2 ou 3
        $adherents = Utilisateur::selectRaw('urs_id, COUNT(id) as nb')
            ->whereIn('statut', [2, 3])
            ->groupBy('urs_id')
            ->get();
        foreach ($adherents as $adherent) {
            $tab_votes[$adherent->urs_id]['nb_adherents'] = $adherent->nb;
        }

        // on récupère le nombre de clubs par UR
        $clubs = Club::selectRaw('urs_id, COUNT(id) as nb')
            ->where('statut', 2)
            ->groupBy('urs_id')
            ->get();
        foreach ($clubs as $club) {
            $tab_votes[$club->urs_id]['nb_clubs'] = $club->nb;
        }

        // on récupère le nombre d'adhérents ayant voté dans la pahse en cours
        $votes_phase_1 = null;
        $votes_phase_2 = null;
        $votes_phase_3 = null;
        $votes = DB::table('votes_utilisateurs')
            ->join('utilisateurs', 'utilisateurs.id', '=', 'votes_utilisateurs.utilisateurs_id')
            ->selectRaw('utilisateurs.urs_id, COUNT(votes_utilisateurs.utilisateurs_id) as nb')
            ->where('votes_utilisateurs.votes_id', $vote->id)
            ->where('votes_utilisateurs.statut', 1)
            ->groupBy('utilisateurs.urs_id')
            ->get();

        if ($vote->phase > 1) {
            $table1 = 'votes_utilisateurs_'.$vote->id.'_phase_1';
            $votes_phase_1 = DB::table($table1)
                ->join('utilisateurs', 'utilisateurs.id', '=', $table1.'.utilisateurs_id')
                ->selectRaw('utilisateurs.urs_id, COUNT(utilisateurs_id) as nb')
                ->where($table1.'.votes_id', $vote->id)
                ->where($table1.'.statut', 1)
                ->groupBy('utilisateurs.urs_id')
                ->get();

            // on regarde dans la table cumul_votes_clubs le nombre de clubs de l'ur ayant voté et le nombre de voix
            $votes_phase_2 = DB::table('cumul_votes_clubs')
                ->join('clubs', 'clubs.id', '=', 'cumul_votes_clubs.clubs_id')
                ->selectRaw('clubs.urs_id, COUNT(clubs_id) as nb_clubs, SUM(nb_voix) as nb_voix')
                ->where('cumul_votes_clubs.votes_id', $vote->id)
                ->where('cumul_votes_clubs.statut', 1)
                ->where('cumul_votes_clubs.pouvoir', 0)
                ->where('clubs.statut', 2)
                ->groupBy('clubs.urs_id')
                ->get();

            if ($vote->phase > 2) {
                // on regarde dans la table cumul_votes_urs le nombre de voix pour l'ur
                $votes_phase_3 = DB::table('cumul_votes_urs')
                    ->selectRaw('urs_id, nb_voix')
                    ->where('votes_id', $vote->id)
                    ->where('statut', 1)
                    ->get();
            }
        } else {
            $votes_phase_1 = $votes;
        }

//        if ($vote->phase == 2) {
//            $votes_phase_2 = $votes;
//        }
//        if ($vote->phase == 3) {
//            $votes_phase_3 = $votes;
//        }



        foreach($votes_phase_1 as $vote_phase1) {
            $tab_votes[$vote_phase1->urs_id]['nb_adherents_phase1'] = $vote_phase1->nb;
            $tab_votes[$vote_phase1->urs_id]['pourcentage_voix_phase1'] = round($vote_phase1->nb * 100 / $tab_votes[$vote_phase1->urs_id]['nb_adherents'], 0);
        }
        if ($votes_phase_2 != null) {
            foreach($votes_phase_2 as $vote_phase2) {
                $tab_votes[$vote_phase2->urs_id]['nb_clubs_phase2'] = $vote_phase2->nb_clubs;
                $tab_votes[$vote_phase2->urs_id]['nb_adherents_phase2'] = $vote_phase2->nb_voix - $vote_phase2->nb_clubs;
                $nb_votes_phase_1 = $tab_votes[$vote_phase2->urs_id]['nb_adherents_phase1'] ?? 0;
                $total_votes = $tab_votes[$vote_phase2->urs_id]['nb_adherents'] + $tab_votes[$vote_phase2->urs_id]['nb_clubs'];
                $tab_votes[$vote_phase2->urs_id]['pourcentage_voix_phase2'] = round(($vote_phase2->nb_voix + $nb_votes_phase_1) * 100 / ($total_votes), 0);
            }
        }
        if ($votes_phase_3 != null) {
            foreach($votes_phase_3 as $vote_phase3) {
                $tab_votes[$vote_phase3->urs_id]['nb_voix_phase3'] = $vote_phase3->nb_voix;
                $nb_votes_phase_2 = $tab_votes[$vote_phase3->urs_id]['nb_clubs_phase2'] ? $tab_votes[$vote_phase3->urs_id]['nb_clubs_phase2'] + $tab_votes[$vote_phase3->urs_id]['nb_adherents_phase2'] : 0;
                $nb_votes_phase_1 = $tab_votes[$vote_phase3->urs_id]['nb_adherents_phase1'] ?? 0;
                $total_votes = $tab_votes[$vote_phase3->urs_id]['nb_adherents'] + $tab_votes[$vote_phase3->urs_id]['nb_clubs'];
                $tab_votes[$vote_phase3->urs_id]['pourcentage_voix_phase3'] = round(($vote_phase3->nb_voix + $nb_votes_phase_2 + $nb_votes_phase_1) * 100 / ($total_votes), 0);
            }
        }
        $nb_adherents = 0; $nb_clubs = 0; $nb_adherents_phase1 = 0; $nb_clubs_phase2 = 0; $nb_adherents_phase2 = 0; $nb_voix_phase3 = 0;
        foreach ($tab_votes as $k => $tab_vote) {
            if (!isset($tab_vote['nb_adherents_phase1'])) {
                $tab_votes[$k]['nb_adherents_phase1'] = 0;
                $tab_votes[$k]['pourcentage_voix_phase1'] = 0;
            }
            if (!isset($tab_vote['nb_clubs_phase2'])) {
                $tab_votes[$k]['nb_clubs_phase2'] = 0;
                $tab_votes[$k]['nb_adherents_phase2'] = 0;
                $nb_votes_phase_1 = $tab_votes[$k]['nb_adherents_phase1'];
                $total_votes = $tab_votes[$k]['nb_adherents'] + $tab_votes[$k]['nb_clubs'];
                $tab_votes[$k]['pourcentage_voix_phase2'] = round($nb_votes_phase_1 * 100 / $total_votes, 0);
            }
            if (!isset($tab_vote['nb_voix_phase3'])) {
                $tab_votes[$k]['nb_voix_phase3'] = 0;
                $nb_votes_phase_1 = $tab_votes[$k]['nb_adherents_phase1'];
                $nb_votes_phase_2 = $tab_votes[$k]['nb_clubs_phase2'] + $tab_votes[$k]['nb_adherents_phase2'];
                $total_votes = $tab_votes[$k]['nb_adherents'] + $tab_votes[$k]['nb_clubs'];
                $tab_votes[$k]['pourcentage_voix_phase3'] = round(($nb_votes_phase_1 + $nb_votes_phase_2) * 100 / $total_votes, 0);
            }
            $nb_adherents += $tab_votes[$k]['nb_adherents'];
            $nb_clubs += $tab_votes[$k]['nb_clubs'];
            $nb_adherents_phase1 += $tab_votes[$k]['nb_adherents_phase1'];
            $nb_clubs_phase2 += $tab_votes[$k]['nb_clubs_phase2'];
            $nb_adherents_phase2 += $tab_votes[$k]['nb_adherents_phase2'];
            $nb_voix_phase3 += $tab_votes[$k]['nb_voix_phase3'];
        }
        $tab_votes['total']['nb_adherents'] = $nb_adherents;
        $tab_votes['total']['nb_clubs'] = $nb_clubs;
        $tab_votes['total']['nb_adherents_phase1'] = $nb_adherents_phase1;
        $tab_votes['total']['nb_clubs_phase2'] = $nb_clubs_phase2;
        $tab_votes['total']['nb_adherents_phase2'] = $nb_adherents_phase2;
        $tab_votes['total']['nb_voix_phase3'] = $nb_voix_phase3;
        $tab_votes['total']['pourcentage_voix_phase1'] = round($nb_adherents_phase1 * 100 / $nb_adherents, 0);
        $tab_votes['total']['pourcentage_voix_phase2'] = round(($nb_adherents_phase1 + $nb_clubs_phase2 + $nb_adherents_phase2) * 100 / ($nb_adherents + $nb_clubs), 0);
        $tab_votes['total']['pourcentage_voix_phase3'] = round(($nb_adherents_phase1 + $nb_clubs_phase2 + $nb_adherents_phase2 + $nb_voix_phase3) * 100 / ($nb_adherents + $nb_clubs), 0);
        return [$vote, $tab_votes];
    }


    public function getVoteDetailByUr($vote, $ur_id) {
        // on récupère le nombre d'adhérents par clubs - nombre d'utilisateurs ayant le statut 2 ou 3
        $adherents = Utilisateur::join('clubs', 'clubs.id', '=', 'utilisateurs.clubs_id')
            ->selectRaw('clubs.id, clubs.numero, clubs.nom, COUNT(utilisateurs.id) as nb')
            ->whereIn('utilisateurs.statut', [2,3])
            ->where('clubs.statut', 2)
            ->where('clubs.urs_id', $ur_id)
            ->groupBy('clubs.id')
            ->orderBy('clubs.numero')
            ->get();

        // on récupère le nombre d'individuels de l'UR
        $nb_individuels = Utilisateur::selectRaw('COUNT(id) as nb')
            ->whereIn('statut', [2, 3])
            ->whereNull('clubs_id')
            ->where('urs_id', $ur_id)
            ->first();

        // on cherche le nombre d'individuels ayant voté
        $table1 = $vote->phase == 1 ? 'votes_utilisateurs' : 'votes_utilisateurs_'.$vote->id.'_phase_1';
        $vote_individuels = DB::table($table1)
            ->join('utilisateurs', 'utilisateurs.id', '=', $table1.'.utilisateurs_id')
            ->selectRaw('COUNT(utilisateurs_id) as nb')
            ->whereIn('utilisateurs.statut', [2, 3])
            ->where('utilisateurs.urs_id', $ur_id)
            ->whereNull('utilisateurs.clubs_id')
            ->where($table1.'.votes_id', $vote->id)
            ->where($table1.'.statut', 1)
            ->first();


        $tab_votes = [];
        $tab_votes[0]['nom'] = 'Individuels';
        $tab_votes[0]['numero'] = 0;
        $tab_votes[0]['nb'] = $nb_individuels->nb;
        $tab_votes[0]['nb_voix'] = $vote_individuels->nb;
        $tab_votes[0]['vote_club'] = 0;
        foreach ($adherents as $adherent) {
            $tab_votes[$adherent->id]['nom'] = $adherent->nom;
            $tab_votes[$adherent->id]['numero'] = $adherent->numero;
            $tab_votes[$adherent->id]['nb'] = $adherent->nb;

            $table1 = $vote->phase == 1 ? 'votes_utilisateurs' : 'votes_utilisateurs_'.$vote->id.'_phase_1';
            $vote_P1 = DB::table($table1)
                ->join('utilisateurs', 'utilisateurs.id', '=', $table1.'.utilisateurs_id')
                ->selectRaw('COUNT(utilisateurs_id) as nb')
                ->where('utilisateurs.clubs_id', $adherent->id)
                ->where($table1.'.votes_id', $vote->id)
                ->where($table1.'.statut', 1)
                ->first();
            $tab_votes[$adherent->id]['nb_voix'] = $vote_P1->nb;
            $tab_votes[$adherent->id]['nb_voix_P1'] = $vote_P1->nb;

            if ($vote->phase > 1) {
                // on regarde dans la table cumul_vote le nb de voix et si le club a voté
                $vote_club = DB::table('cumul_votes_clubs')
                    ->selectRaw('nb_voix, statut, pouvoir')
                    ->where('clubs_id', $adherent->id)
                    ->where('votes_id', $vote->id)
                    ->first();
                if (!$vote_club) {
                    $tab_votes[$adherent->id]['vote_club'] = 0;
                    $tab_votes[$adherent->id]['pouvoir'] = 0;
                    $tab_votes[$adherent->id]['nb_voix_pouvoir'] = 0;
                } else {
                    if ($vote_club->statut == 1 && $vote_club->pouvoir == 0 && $vote_club->nb_voix > 0) {
                        $tab_votes[$adherent->id]['nb_voix'] += $vote_club->nb_voix - 1;
                        $tab_votes[$adherent->id]['vote_club'] = 1;
                        $tab_votes[$adherent->id]['pouvoir'] = 0;
                        $tab_votes[$adherent->id]['nb_voix_pouvoir'] = 0;
                    } else {
                        $tab_votes[$adherent->id]['vote_club'] = 0;
                        $tab_votes[$adherent->id]['pouvoir'] = $vote_club->pouvoir;
                        $tab_votes[$adherent->id]['nb_voix_pouvoir'] = $vote_club->nb_voix;
                    }
                }
            }

            if ($vote->phase > 2 && $tab_votes[$adherent->id]['pouvoir'] == 1) {
                // le club a donné son pouvoir, on regarde si l'UR a voté
                $vote_UR = DB::table('cumul_votes_urs')
                    ->selectRaw('statut')
                    ->where('urs_id', $ur_id)
                    ->where('votes_id', $vote->id)
                    ->first();
                if ($vote_UR->statut == 1) {
                    $tab_votes[$adherent->id]['nb_voix'] = $tab_votes[$adherent->id]['nb_voix_P1'] + $tab_votes[$adherent->id]['nb_voix_pouvoir'] - 1;
                    $tab_votes[$adherent->id]['vote_club'] = 1;
                }
            }
        }
        foreach ($tab_votes as $k => $tab_vote) {
            // on calcule le poyurcentage
            if ($tab_vote['nb'] + $tab_vote['vote_club'] > 0) {
                $tab_votes[$k]['pourcentage'] = round($tab_vote['nb_voix'] * 100 / ($tab_vote['nb'] + $tab_vote['vote_club']), 0);
            } else {
                $tab_votes[$k]['pourcentage'] = 0;
            }
        }
        return $tab_votes;
    }

    public function getNotVotedAdherents($club) {
        $fin = date('Y-m-d', strtotime('-7 days'));
        $vote = Vote::where('type', 1)->where('phase', '>', 0)->where('urs_id', 0)->where('fin', '>=', $fin)->first();
        if (!$vote) {
            return [null, []];
        }
        if ($vote->phase > 1) {
            $vote_club = DB::table('cumul_votes_clubs')
                ->where('votes_id', $vote->id)
                ->where('clubs_id', $club->id)
                ->first();
            if (!$vote_club) {
                return [$vote, []];
            }
            // si le club a voté sans donner son pouvoir, tous les adhérents ont voté
            if ($vote_club->statut == 1 && $vote_club->pouvoir == 0) {
                return [$vote, []];
            }
            if ($vote->phase > 2 && $vote_club->pouvoir == 1 && $vote_club->statut == 1) {
                $vote_UR = DB::table('cumul_votes_urs')
                    ->selectRaw('statut')
                    ->where('urs_id', $club->urs_id)
                    ->where('votes_id', $vote->id)
                    ->first();
                if (!$vote_UR) {
                    return [$vote, []];
                }
                // si l'ur a voté alors que le club lui a donné son pouvoir, tous les adhérents ont voté
                if ($vote_UR->statut == 1) {
                    return [$vote, []];
                }
            }
        }


        // on récupère tous les adhérents valides du club
        $utilisateurs = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->where('clubs_id', $club->id)
            ->whereIn('statut', [2,3])
            ->orderBy('personnes.nom')
            ->orderBy('personnes.prenom')
            ->get();


        if ($vote->phase == 1) {
            $table = 'votes_utilisateurs';
        }
        else {
            $table = 'votes_utilisateurs_'.$vote->id.'_phase_1';
        }
        foreach ($utilisateurs as $k => $utilisateur) {
            // on regarde si l'utuilisateur a déjà voté
            $exist_vote = DB::table($table)
                ->where('votes_id', $vote->id)
                ->where('utilisateurs_id', $utilisateur->id)
                ->where('statut', 1)
                ->first();
            if ($exist_vote) {
                unset($utilisateurs[$k]);
            }
        }
        return [$vote, $utilisateurs];
    }

}
