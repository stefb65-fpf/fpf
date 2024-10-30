<?php

namespace App\Console\Commands;

use App\Concern\Invoice;
use App\Mail\SendUtilisateurCreateByAdmin;
use App\Mail\TestMail;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class testStephane extends Command
{
    use Invoice;
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
        // on récupère les enregistrements DNS du domaine federation-photo.fr
//        $dnsRecords = dns_get_record('federation-photo.fr', DNS_ALL);

//        // on récupère les enregistrements DNS DKIM
//        $dnsRecords = dns_get_record('dkim._domainkey.federation-photo.fr', DNS_ALL);
//        dd($dnsRecords);

//        $mailSent = Mail::to('stefb65@gmail.com')->send(new TestMail());
//        die();

        // on prend tous les règlements validés pour les clubs depuis le 01/09/2024
//        $reglements = Reglement::where('statut', 1)
//            ->whereNotNull('clubs_id')
//            ->where('dateenregistrement', '>=', '2024-09-01')
//            ->get();
        $reglements = Reglement::where('id', 37847)->get();
        foreach ($reglements as $reglement) {
            // on cherche la facture avec la référence du règlement
            $description = "Renouvellement des adhésions et abonnements pour le club";
            $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'club_id' => $reglement->clubs_id, 'renew_club' => 1];
            $this->correctInvoice($datai);
        }
//        die();
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
//        dd($nb_correction, $delta);
    }
}
