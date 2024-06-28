<?php

namespace App\Console\Commands;

use App\Models\Fonction;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanFonctionUr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:fonctionsurs';

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
        // on récupère toutes les focntions de niveau 2 pour lequel urs_id est renseigné
        $fonctions = Fonction::where('instance', 2)->where('urs_id', '!=', 0)->get();
        $nb_to_delete = 0;
        foreach ($fonctions as $fonction) {
            // on regarde si cette fonction est occupée par un utilisateur
            $fonctionuser = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctions_id', $fonction->id)->first();
            if (!$fonctionuser) {
                // on supprime la fonction dans la table fonctionsurs
                DB::table('fonctionsurs')->where('fonctions_id', $fonction->id)->delete();

                // on supprime la fonction dans la table fonction
                $fonction->delete();
                $nb_to_delete++;
            }
        }

        echo "to delete: $nb_to_delete\n";
    }
}
