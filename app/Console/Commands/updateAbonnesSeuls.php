<?php

namespace App\Console\Commands;

use App\Models\Abonnement;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Console\Command;

class updateAbonnesSeuls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:abonnes';

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
//        $utilisateurs = Utilisateur::whereNotNull('personne_id')->where('urs_id', 0)->get();
//        foreach ($utilisateurs as $utilisateur) {
//            $personne = $utilisateur->personne;
//            $data = array('is_adherent' => 0, 'is_abonne' => 1);
//            $personne->update($data);
//        }

        $personnes = Personne::where('is_adherent', 0)->where('is_abonne', 1)->get();
        foreach ($personnes as $personne) {
            $abo = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
            if (!$abo) {
                $data = array('is_abonne' => 0);
                $personne->update($data);
            }
        }
    }
}
