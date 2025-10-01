<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateVoteUr12 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:voteur12';

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
        $vote = Vote::where('id', 82)->first();
        if (intval(date('m')) < 9) {
            $authorised_status = [2];
        } else {
            $authorised_status = [0,1,2];
        }

//        $tab_clubs = [];
        $clubs = Club::whereIn('statut', $authorised_status)->where('urs_id', $vote->urs_id)->get();
//        foreach ($clubs as $club) {
//            $tab_clubs[] = $club->id;
//        }

        $exist_clubs = [];
        $cumul_clubs = DB::table('cumul_votes_clubs')->where('votes_id', $vote->id)->get();
        foreach ($cumul_clubs as $c) {
            $exist_clubs[] = $c->clubs_id;
//            if (!in_array($c->clubs_id, $tab_clubs)) {
//                DB::table('cumul_votes_clubs')->where('votes_id', $vote->id)->where('clubs_id', $c->clubs_id)->delete();
//            }
        }
        foreach ($clubs as $club) {
            if (!in_array($club->id, $exist_clubs)) {
                // on traite
                if (intval(date('m')) < 9) {
                    $authorised_status_adherents = [2,3];
                } else {
                    $authorised_status_adherents = [0, 1, 2, 3];
                }
                $utilisateurs = Utilisateur::whereIn('statut', $authorised_status_adherents)->where('clubs_id', $club->id)->get();

                $nb_voix = 1;
                foreach ($utilisateurs as $utilisateur) {
                    // on regarde si l'adhérent a vote
                    $exist_vote_utilisateur = DB::table('votes_utilisateurs_82_phase_1')
                        ->where('votes_id', $vote->id)
                        ->where('utilisateurs_id', $utilisateur->id)
                        ->where('statut', 1)
                        ->first();
                    if (!$exist_vote_utilisateur) {
                        $nb_voix++;
                    }
                }

                $datacvc = array('votes_id' => $vote->id, 'clubs_id' => $club->id, 'statut' => 0, 'nb_voix' => $nb_voix, 'updated_at' => date('Y-m-d'), 'pouvoir' => 0);
                DB::table('cumul_votes_clubs')->insert($datacvc);
            }
        }

    }
}
