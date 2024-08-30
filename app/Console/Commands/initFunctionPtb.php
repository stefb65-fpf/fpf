<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class initFunctionPtb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:ptb';

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
        // on récupère les utilisateurs ayant un statut 2 ou 3 et une fonction contact club, président d'UR ou webmestre UR
        $users = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->join('personnes', 'utilisateurs.personne_id', '=', 'personnes.id')
            ->whereIn('utilisateurs.statut', [2, 3])
            ->whereIn('fonctionsutilisateurs.fonctions_id', [57, 58, 97, 87, 336])
            ->selectRaw('DISTINCT personnes.id, personnes.nom, personnes.prenom, personnes.email')
            ->get();
        foreach ($users as $user) {
            // on recherche le user ID dans la table wp_users de wordpress
            $wp_user = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE user_email = '" . $user->email. "' ORDER BY ID DESC LIMIT 1");
            if (isset($wp_user[0])) {
                // on cherche le champ meta_value pour le user_id et la meta_key wp_capabilities dans la table wp_usermeta
                $wp_capabilities = DB::connection('mysqlwp')->select("SELECT meta_value FROM wp_usermeta WHERE user_id = " . $wp_user[0]->ID . " AND meta_key = 'wp_capabilities' LIMIT 1");
                if (isset($wp_capabilities[0])) {
                    // on récupère le contenu du champ meta_value
                    $wp_capabilities = unserialize($wp_capabilities[0]->meta_value);
                    // on ajoute les droits de l'utilisateur si ils n'existent pas déjà
                    if (!isset($wp_capabilities['ptb_clubsur_author'])) {
                        $wp_capabilities['ptb_clubsur_author'] = true;
                    }
                    // on met à jour le champ meta_value
                    DB::connection('mysqlwp')->update("UPDATE wp_usermeta SET meta_value = '" . serialize($wp_capabilities) . "' WHERE user_id = " . $wp_user[0]->ID . " AND meta_key = 'wp_capabilities'");
                }
            }
        }
    }
}
