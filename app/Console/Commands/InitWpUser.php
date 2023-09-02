<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitWpUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:wpuser';

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
//        $wp_users = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE ID NOT IN (1,6443,8919,2,3491,3913,5373,6639,15353)");
//        $tokeep = 0;
//        foreach ($wp_users as $wp_user) {
//            // on regarde si le user est présent dans la table wp_wc_customer_lookup
//            $wp_wc_customer_lookup = DB::connection('mysqlwp')->select("SELECT customer_id FROM wp_wc_customer_lookup WHERE user_id = $wp_user->ID LIMIT 1");
//            if (sizeof($wp_wc_customer_lookup) == 0) {
//                // on regarde si le user est présent dans la table wp_usermeta avec meta_key = 'wp_capabilities'
//                $wp_usermeta = DB::connection('mysqlwp')->select("SELECT meta_value FROM wp_usermeta WHERE user_id = $wp_user->ID AND meta_key = 'wp_capabilities' LIMIT 1");
//                // est ce que la chaine de caractère contient s:3:"ptb"
//                if (str_contains($wp_usermeta[0]->meta_value, 's:3:"ptb"')) {
//                    $tokeep++;
//                } else {
//                    // on supprime le user de la table wp_users
////                    DB::connection('mysqlwp')->statement("DELETE FROM wp_users WHERE ID = $wp_user->ID LIMIT 1");
////                    // on supprime le user de la table wp_usermeta
////                    DB::connection('mysqlwp')->statement("DELETE FROM wp_usermeta WHERE user_id = $wp_user->ID");
//                }
//            }
//        }
//        dd($tokeep);

        $utilisateurs = Utilisateur::where('saison', 2023)->get();
        $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*";
        $shuffle_letters = str_shuffle($letters);
        $random_password = substr($shuffle_letters, 0, 8);
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->personne) {
//                $this->insertWpUser($utilisateur->personne->prenom, $utilisateur->personne->nom, $utilisateur->personne->email, $random_password);
            }
        }
    }
}
