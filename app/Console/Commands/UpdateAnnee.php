<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateAnnee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:annee';

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
        try {
            DB::beginTransaction();

            // on change la valeur saison pour tout utilisateur ayant un statut 2 ou 3
            Utilisateur::whereIn('statut', [2, 3])->update(['saison' => date('Y')]);

            // on sauvegarde uis vide la table souscriptions
            DB::statement("DROP TABLE IF EXISTS souscriptions_prec");
            DB::statement("CREATE TABLE souscriptions_prec LIKE souscriptions");
            DB::statement("INSERT souscriptions_prec SELECT * FROM souscriptions");

        // on supprime du site fédéral les clubs qui ne sont pas en statut 2
        $clubs = Club::where('statut', 2)->get();
        $tabclubs = [];
        foreach ($clubs as $club) {
            $tabclubs[$club->numero] = $club->id;
        }

        $wp_clubs = DB::connection('mysqlwp')->select("SELECT P.ID, M.meta_value FROM wp_posts P, wp_postmeta M WHERE M.post_id = P.id AND P.post_type = 'club-ur' AND M.meta_key = 'ptb_numero'");
        foreach ($wp_clubs as $wp_club) {
            $numero = $wp_club->meta_value;
            if (!isset($tabclubs[$numero])) {
                // on supprime des tables wp_posts et wp_postmeta
                $post_id = $wp_club->ID;
                DB::connection('mysqlwp')->statement("DELETE FROM wp_posts WHERE ID = $post_id LIMIT 1");
                DB::connection('mysqlwp')->statement("DELETE FROM wp_postmeta WHERE post_id = $post_id");
            }
        }

        $personnes = Personne::where('is_adherent', 1)->selectRaw('id, email')->get();
        $tab_personnes = [];
        foreach ($personnes as $personne) {
            $tab_personnes[$personne->email] = $personne->id;
        }
        $wp_users = DB::connection('mysqlwp')->select("SELECT DISTINCT U.ID, U.user_nicename FROM wp_users U, wp_usermeta M WHERE M.user_id = U.id AND M.meta_key = 'wp_user_level' AND M.meta_value = 0");
        foreach ($wp_users as $wp_user) {
            if (!isset($tab_personnes[$wp_user->user_nicename])) {
                // on supprime des tables wp_posts et wp_postmeta
                $user_id = $wp_user->ID;
                DB::connection('mysqlwp')->statement("DELETE FROM wp_users WHERE ID = $user_id LIMIT 1");
                DB::connection('mysqlwp')->statement("DELETE FROM wp_usermeta WHERE user_id = $user_id");
            }
        }

            DB::commit();
            Mail::raw('Le changement a bien effectué', function ($message) {
                $message->to('contact@episteme-web.com')
                    ->subject('Changement année FPF');
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Mail::raw('Une erreur est survenue lors du changement '.$e->getMessage(), function ($message) {
                $message->to('contact@episteme-web.com')
                    ->subject('Changement année FPF');
            });
        }
    }
}
