<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateAuthorAnnee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:author';

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
        // on récupère dans wordpress tous les email des users ayant la chaine ptb_clubsur_author dans wp_capabilities de wp_usermeta
        $wp_users = DB::connection('mysqlwp')->select("SELECT U.ID, U.user_email, M.meta_value FROM wp_users U, wp_usermeta M WHERE U.ID = M.user_id AND M.meta_key = 'wp_capabilities' AND M.meta_value LIKE '%ptb_clubsur_author%'");

        // pour chaque user récupéré, on regarde si il existe dans la table utilisateurs avec un statut 2 ou 3 et s'il a une fonction club our OU
        foreach ($wp_users as $wp_user) {
            $user = Utilisateur::join('personnes', 'utilisateurs.personne_id', '=', 'personnes.id')
                ->join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
                ->whereIn('utilisateurs.statut', [2, 3])
                ->whereIn('fonctionsutilisateurs.fonctions_id', [57, 58, 97, 87, 336, 320])
                ->where('personnes.email', $wp_user->user_email)
                ->first();
            if (!$user) {
               // on supprime de wp_capabilities la chaine ptb_clubsur_author
                $wp_capabilities = unserialize($wp_user->meta_value);
                unset($wp_capabilities['ptb_clubsur_author']);
                DB::connection('mysqlwp')->update("UPDATE wp_usermeta SET meta_value = '" . serialize($wp_capabilities) . "' WHERE user_id = " . $wp_user->ID . " AND meta_key = 'wp_capabilities'");
            }
        }
    }
}
