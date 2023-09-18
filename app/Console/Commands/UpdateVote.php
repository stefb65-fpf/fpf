<?php

namespace App\Console\Commands;

use App\Concern\Tools;
use App\Models\Club;
use App\Models\Ur;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateVote extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:vote';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // on met à jour les votes pour lesquelles la date de debut  est égale à la date du jour
        Vote::where('debut', date('Y-m-d'))->where('type', 1)->where('phase', 0)->update(['phase' => 1]);

        // on met à jour les votes pour lesquelles la date de debut phase 2 est égale à la date du jour
        $votes = Vote::where('debut_phase2', date('Y-m-d'))->where('type', 1)->get();
        foreach ($votes as $vote) {
            $exist_vote_club = DB::table('votes_clubs')->where('votes_id', $vote->id)->first();
            if ($exist_vote_club) {
                Mail::raw("Problème club présent sur alimentation cumul_vote_club $vote->id", function ($message) {
                    $message->to('contact@episteme-web.com')
                        ->subject('Problème club présent sur alimentation cumul_vote_club');
                });
            } else {
                // on cherche tous les clubs valides
                $clubs = Club::where('statut', 2)->get();
                foreach ($clubs as $club) {
                    // on recherche tous les adhérents dans ce club
                    $utilisateurs = Utilisateur::whereIn('statut', [2,3])->where('clubs_id', $club->id)->get();

                    $nb_voix = 1;
                    foreach ($utilisateurs as $utilisateur) {
                        // on regarde si l'adhérent a vote
                        $exist_vote_utilisateur = DB::table('votes_utilisateurs')
                            ->where('votes_id', $vote->id)
                            ->where('utilisateurs_id', $utilisateur->id)
                            ->where('statut', 1)
                            ->first();
                        if (!$exist_vote_utilisateur) {
                            $nb_voix++;
                        }
                    }

                    // on insère dans la table cumul_votes_club
                    $datacvc = array('votes_id' => $vote->id, 'clubs_id' => $club->id, 'statut' => 0, 'nb_voix' => $nb_voix, 'updated_at' => date('Y-m-d'), 'pouvoir' => 0);
                    DB::table('cumul_votes_clubs')->insert($datacvc);

                    // on clone la table votes_utilisateurs
                    $tabledest = 'votes_utilisateurs_'.$vote->id.'_phase_1';
                    DB::statement("DROP TABLE IF EXISTS ".$tabledest);
                    DB::statement("CREATE TABLE $tabledest LIKE votes_utilisateurs");
                    $statement = "INSERT $tabledest SELECT * FROM votes_utilisateurs";
                    DB::statement($statement);

                    // on supprime de la la tabble clonée tout ce qui ne concerne pas le vote
                    DB::table($tabledest)->where('votes_id', '!=', $vote->id)->delete();

                    // on supprime de la table votes_utilisateurs tout ce qui concerne le vote
                    DB::table('votes_utilisateurs')->where('votes_id', $vote->id)->delete();
                }
                // on passe le vote en phase 2
                $vote->update(['phase' => 2]);
            }
        }



        // on met à jour les votes pour lesquels la date de debut phase 3 est égale à la date du jour
        $votes_phase3 = Vote::where('debut_phase3', date('Y-m-d'))->where('type', 1)->get();
        if (sizeof($votes_phase3) > 0) {
            // on calcule la limite pour les urs
            $nb_adherents_fpf = Utilisateur::whereIn('statut', [2,3])->whereNotNull('clubs_id')->count();
            $limit_ur = floor($nb_adherents_fpf / 25);

            foreach ($votes_phase3 as $vote) {
                // on regarde s'il y a déjà des éléments pour ce vote dans la table cimul_votes_urs
                $votes_urs = DB::table('cumul_votes_urs')->where('votes_id', $vote->id)->first();
                if ($votes_urs) {
                    Mail::raw("Problème ur présent sur alimentation cumul_vote_ur $vote->id", function ($message) {
                        $message->to('contact@episteme-web.com')
                            ->subject('Problème ur présent sur alimentation cumul_vote_ur');
                    });
                } else {
                    $urs = Ur::all();
                    foreach ($urs as $ur) {
                        // on va prendre tous les clubs de l'ur qui n'ont pas voté
                        $nb_cumul_urs = DB::table('cumul_votes_clubs')
                            ->join('clubs', 'clubs.id', '=', 'cumul_votes_clubs.clubs_id')
                            ->where('cumul_votes_clubs.votes_id', $vote->id)
                            ->where('cumul_votes_clubs.statut', 1)
                            ->where('clubs.urs_id', $ur->id)
                            ->where('cumul_votes_clubs.pouvoir', 1)
                            ->sum('cumul_votes_clubs.nb_voix');
                        $nb_voix_ur = 0;
                        if ($nb_cumul_urs) {
                            $nb_voix_ur = min($nb_cumul_urs, $limit_ur);
                        }

                        $datacvu = array('votes_id' => $vote->id, 'urs_id' => $ur->id, 'statut' => 0, 'nb_voix' => $nb_voix_ur, 'updated_at' => date('Y-m-d'));
                        DB::table('cumul_votes_urs')->insert($datacvu);

                        // on clone la table votes_utilisateurs
                        $tabledest = 'votes_utilisateurs_'.$vote->id.'_phase_2';
                        DB::statement("DROP TABLE IF EXISTS ".$tabledest);
                        DB::statement("CREATE TABLE $tabledest LIKE votes_utilisateurs");
                        $statement = "INSERT $tabledest SELECT * FROM votes_utilisateurs";
                        DB::statement($statement);

                        // on supprime de la la tabble clonée tout ce qui ne concerne pas le vote
                        DB::table($tabledest)->where('votes_id', '!=', $vote->id)->delete();

                        // on supprime de la table votes_utilisateurs tout ce qui concerne le vote
                        DB::table('votes_utilisateurs')->where('votes_id', $vote->id)->delete();
                    }
                }

                // on passe le vote en phase 3
                $vote->update(['phase' => 3]);
            }
        }
    }
}
