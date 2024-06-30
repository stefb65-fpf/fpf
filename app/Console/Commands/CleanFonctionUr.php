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
//        $fonctions = Fonction::where('instance', 2)->where('urs_id', '!=', 0)->get();
//        $nb_to_delete = 0;
//        foreach ($fonctions as $fonction) {
//            // on regarde si cette fonction est occupée par un utilisateur
//            $fonctionuser = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
//                ->where('fonctions_id', $fonction->id)->first();
//            if (!$fonctionuser) {
//                // on supprime la fonction dans la table fonctionsurs
//                DB::table('fonctionsurs')->where('fonctions_id', $fonction->id)->delete();
//
//                // on supprime la fonction dans la table fonction
//                $fonction->delete();
//                $nb_to_delete++;
//            }
//        }
//
//        echo "to delete: $nb_to_delete\n";

        // on prends toutes les fonctions d'instance 2 pour lequel l'ur id est à 0
        $fonctions = Fonction::where('instance', 2)->where('urs_id', 0)->get();

        $to_delete_fpf = 0;
        foreach ($fonctions as $fonction) {
            // pour chaque fonction, on cherche toutes les urs pour lesquelles elle est déclarée dans la table fonctionsurs
            $urs = DB::table('fonctionsurs')->where('fonctions_id', $fonction->id)->get();
            // pour chaque ur, on regarde si la fonction correspondante est déclarée dans la table fonctionsutilisateurs
            foreach ($urs as $ur) {
                $fonctionuser = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                    ->where('fonctions_id', $fonction->id)->where('urs_id', $ur->urs_id)->first();
                if (!$fonctionuser) {
                    $to_delete_fpf++;
                    dd($fonction);
//                    // on met à jour l'urs_id
//                    $fonction->urs_id = $fonctionuser->urs_id;
//                    $fonction->save();
                }
            }
//            dd($urs);
//            $fonctionuser = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
//                ->where('fonctions_id', $fonction->id)->first();
//            if ($fonctionuser) {
//                // on met à jour l'urs_id
//                $fonction->urs_id = $fonctionuser->urs_id;
//                $fonction->save();
//            }
        }

        echo "to delete: $to_delete_fpf\n";
    }
}
