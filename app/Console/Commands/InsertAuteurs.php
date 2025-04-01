<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertAuteurs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:auteurs';

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
        // on récupère tous les utilisateurs avec un statut de 2 ou 3 et une fonction dans la liste 57, 58, 97, 87, 336
        $users = Utilisateur::join('personnes', 'utilisateurs.personne_id', '=', 'personnes.id')
            ->join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->whereIn('utilisateurs.statut', [2, 3])
            ->whereIn('fonctionsutilisateurs.fonctions_id', [57, 58, 97, 87, 336])
            ->get();

        foreach ($users as $user) {
            // on récupère dans la base de wordpress, table users, lu user ayant pour adresse email celle de l'utilisateur
            $email = $user->email;
            $wp_user = DB::connection('mysqlwp')->select("SELECT U.ID, U.user_email FROM wp_users U WHERE U.user_email = '$email' ORDER BY ID DESC LIMIT 1");
            if (isset($wp_user[0])) {
                $userID = $wp_user[0]->ID;

                // on récupère le user_meta wp_capabilities
                $wp_usermeta = DB::connection('mysqlwp')->select("SELECT M.meta_value FROM wp_usermeta M WHERE M.meta_key = 'wp_capabilities' AND M.user_id = '$userID' LIMIT 1");
                if (sizeof($wp_usermeta) > 0) {
                    $wp_capabilities = unserialize($wp_usermeta[0]->meta_value);
                    if (!isset($wp_capabilities['ptb_clubsur_author'])) {
                        $wp_capabilities['ptb_clubsur_author'] = true;
                    }
                    DB::connection('mysqlwp')->statement("UPDATE wp_usermeta SET meta_value = '" . serialize($wp_capabilities) . "' WHERE user_id = " . $userID . " AND meta_key = 'wp_capabilities' LIMIT 1");
                }

            } else {
                var_dump($email);
            }
        }
    }
}
