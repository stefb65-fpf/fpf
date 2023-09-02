<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateSaison extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:saison';

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

        // glissement des paramètres saison
        Configsaison::where('id', 0)->delete();
        Configsaison::where('id', 1)->update(['id' => 0]);
        $config = Configsaison::where('id', 2)->first();
        $datas = $config->getAttributes();
        $datas['datedebut'] = date_add(date_create($datas['datedebut']), date_interval_create_from_date_string('1 year'))->format('Y-m-d');
        $datas['datefin'] = date_add(date_create($datas['datefin']), date_interval_create_from_date_string('1 year'))->format('Y-m-d');
        $datas['datefinadhesion'] = date_add(date_create($datas['datefinadhesion']), date_interval_create_from_date_string('1 year'))->format('Y-m-d');
        $datas['datedebutflorilege'] = date_add(date_create($datas['datedebutflorilege']), date_interval_create_from_date_string('1 year'))->format('Y-m-d');
        $datas['datefinflorilege'] = date_add(date_create($datas['datefinflorilege']), date_interval_create_from_date_string('1 year'))->format('Y-m-d');
        $datas['premiernumeroFP'] = $config->premiernumeroFP + 5;
        $datas['numeroencours'] = $config->premiernumeroFP + 5;
        $config->update(['id' => 1]);
        Configsaison::insert($datas);


        // glissement des tarifs
        Tarif::where('statut', 0)->delete();
        $tarifs = Tarif::where('statut', 1)->get();
        foreach ($tarifs as $tarif) {
            $datas = $tarif->getAttributes();
            $datas['statut'] = '0';
            Tarif::insert($datas);
        }

        // sauvegarde des tables clubs, personnes, utilisateurs
        DB::statement("DROP TABLE IF EXISTS clubs_prec");
        DB::statement("CREATE TABLE clubs_prec LIKE clubs");
        DB::statement("INSERT clubs_prec SELECT * FROM clubs");

        DB::statement("DROP TABLE IF EXISTS personnes_prec");
        DB::statement("CREATE TABLE personnes_prec LIKE personnes");
        DB::statement("INSERT personnes_prec SELECT * FROM personnes");

        DB::statement("DROP TABLE IF EXISTS utilisateurs_prec");
        DB::statement("CREATE TABLE utilisateurs_prec LIKE utilisateurs");
        DB::statement("INSERT utilisateurs_prec SELECT * FROM utilisateurs");

        // ************* mise à jour des clubs  *************
        // tous les clubs avec un statut 0 ou 1 passent à un statut 3
        Club::whereIn('statut', [0, 1])->update(['statut' => 3, 'second_year' => 0]);

        // tous les clubs avec un statut 2 passent à un statut 0
        Club::where('statut', 2)->update(['statut' => 0]);

        // tous les clubs nouveaux passent en second year
        Club::where('ct', 'N')->update(['second_year' => 1, 'ct' => 1]);

        // mise à jour des personnes
        // toutes les personnes ayant une valeur is_adherent à 0 passent à 2
//        Personne::where('is_adherent', 0)->update(['is_adherent' => 2]);

        // mise à jour des utilisateurs
        Utilisateur::whereIn('statut', [2, 3])->update(['saison' => date('Y')]);
        Utilisateur::whereIn('statut', [1, 2, 3])->update(['statut' => 0]);

        // on met à jour le type de carte pour les utilisateurs de moins de 25 ans
        $utilisateurs = Utilisateur::where('statut', 0)->whereIn('ct', [3,4,8,9])->get();
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->personne->datenaissance) {
                $date_naissance = new \DateTime($utilisateur->personne->datenaissance);
                $date_now = new \DateTime();
                $age = $date_now->diff($date_naissance)->y;
                $ct = $utilisateur->clubs_id ? 2 : 7;
                if ($age < 18) {
                    $ct = $utilisateur->clubs_id ? 4 : 9;
                } elseif ($age < 25) {
                    $ct = $utilisateur->clubs_id ? 3 : 8;
                }
//                $ct = match ($age) {
//                    $age < 18 => $utilisateur->clubs_id ? 4 : 9,
//                    $age < 25 => $utilisateur->clubs_id ? 3 : 8,
//                    default => $utilisateur->clubs_id ? 2 : 7,
//                };
                if ($ct != $utilisateur->ct) {
                    $utilisateur->update(['ct' => $ct]);
                }
            }
        }

            DB::commit();
            Mail::raw('La saison a bien été mise à jour', function ($message) {
                $message->to('contact@episteme-web.com')
                    ->subject('Mise à jour de la saison');
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Mail::raw('Une erreur est survenue lors de la mise à jour '.$e->getMessage(), function ($message) {
                $message->to('contact@episteme-web.com')
                    ->subject('Mise à jour de la saison');
            });
        }
    }
}
