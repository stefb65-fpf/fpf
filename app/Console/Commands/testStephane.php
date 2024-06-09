<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class testStephane extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stephane';

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
        die();
//        $cumuls_vote_club = DB::table('cumul_votes_clubs')->where('votes_id',62)->get();
//        $nb_correction = 0;
//        $delta = 0;
//        foreach ($cumuls_vote_club as $cumul_vote_club) {
//            $utilisateurs = Utilisateur::whereIn('statut', [2,3])->where('clubs_id', $cumul_vote_club->clubs_id)->get();
//            $nb_voix = 1;
//            foreach ($utilisateurs as $utilisateur) {
//                // on regarde si l'adhérent a vote
//                $exist_vote_utilisateur = DB::table('votes_utilisateurs_62_phase_1')
//                    ->where('utilisateurs_id', $utilisateur->id)
//                    ->where('statut', 1)
//                    ->first();
//                if (!$exist_vote_utilisateur) {
//                    $nb_voix++;
//                }
//            }
//
//            if ($cumul_vote_club->statut == 0 || ($cumul_vote_club->statut == 1 && $cumul_vote_club->pouvoir == 1)) {
//                if ($nb_voix != $cumul_vote_club->nb_voix) {
//                    // on met à jour le cumul
//                    DB::table('cumul_votes_clubs')
//                        ->where('votes_id', 62)
//                        ->where('clubs_id', $cumul_vote_club->clubs_id)
//                        ->update(['nb_voix' => $nb_voix]);
//                    $nb_correction++;
//                }
//            } else {
//                if ($nb_voix != $cumul_vote_club->nb_voix) {
//                    DB::table('cumul_votes_clubs')
//                        ->where('votes_id', 62)
//                        ->where('clubs_id', $cumul_vote_club->clubs_id)
//                        ->update(['nb_voix' => $nb_voix]);
//                    $delta += $cumul_vote_club->nb_voix - $nb_voix;
//                }
//            }
//        }
        dd($nb_correction, $delta);
    }
}
