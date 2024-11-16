<?php

namespace App\Console\Commands;

use App\Mail\Erratum;
use App\Models\Personne;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendRenewAbo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renew:abo';

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
        // on récupère toutes les personnes pour qui le numéro de fin d'abonnement est égal au numéro en cours - 1
        $config_saison = DB::table('configsaisons')->where('id', 1)->first();
        $numero_encours = $config_saison->numeroencours;
        $fin_abonnement = $numero_encours - 1;

        $personnes = Personne::join('abonnements', 'personnes.id', '=', 'abonnements.personne_id')
            ->where('abonnements.fin', $fin_abonnement)
            ->selectRaw('personnes.id, personnes.email')
            ->get();


        $nb = 0;
        foreach ($personnes as $personne) {
            // on regarde si ces personnes n'ont pas un abvonnement en cours
            $abonnement = DB::table('abonnements')
                ->where('personne_id', $personne->id)
                ->where('etat', 1)
                ->first();
            if (!$abonnement) {
                Mail::to($personne->email)->send(new \App\Mail\SendRenewAbo($numero_encours, $fin_abonnement));
//                Mail::to($personne->email)->send(new Erratum());
                usleep(500000);
            }
        }
    }
}
