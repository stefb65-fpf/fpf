<?php

namespace App\Console\Commands;

use App\Concern\Tools;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitUtilisateurs extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:utilisateurs';

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


//        $utilisateurs = Utilisateur::where('statut', 10)->get();
//        foreach ($utilisateurs as $v) {
//            $search = substr($v->identifiant, 0, 8);
//            $max = DB::table('utilisateurs')
//                ->where('statut', '!=', 10)
//                ->where('identifiant', 'like', $search.'%')
//                ->max('numeroutilisateur');
//            if ($max) {
//                $max++;
//            } else {
//                $max = 1;
//            }
//            $new_identifiant = $search.str_pad($max, 4, '0', STR_PAD_LEFT);
//            var_dump($v->identifiant);
//            var_dump($new_identifiant);
//
//            // on met à jour l'identifiant
//            DB::table('utilisateurs')->where('id', $v->id)->update(array('identifiant' => $new_identifiant, 'numeroutilisateur' => $max, 'statut' => 0));
//
//            // on met à jour l'identifiant dans la table photos
//            DB::table('photos')->where('participants_id', $v->identifiant)->update(array('participants_id' => $new_identifiant));
//            DB::table('rphotos')->where('participants_id', $v->identifiant)->update(array('participants_id' => $new_identifiant));
//        }






//        die();
//
//        $utilisateurs = Utilisateur::where('statut', 10)->orderBy('courriel')->get();
//        $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*";
//        foreach ($utilisateurs as $v) {
//            $courriel = trim($v->courriel);
//            $prenom = mb_convert_case(trim($v->prenom), MB_CASE_TITLE, "UTF-8");
//            $nom = mb_strtoupper(trim($v->nom));
//            $random_password = substr($nom, 0, 1).'@'.substr(strtolower($prenom), 0, 1).'54PgT23';
//            $password = $this->encodePwd($random_password);
//            $datap = array(
//                'nom' => $nom,
//                'prenom' => $prenom,
//                'sexe' => $v->sexe,
//                'is_adherent' => 1,
//                'password' => $password,
//                'premiere_connexion' => 1,
//                'news'  => $v->news,
//                'blacklist_date'  => $v->blacklist_date,
//            );
//
//            $is_new = 0;
//            $prec = array('email' => '', 'nom' => '', 'prenom' => '');
//            if ($courriel != '') {
//                $datap['email'] = $courriel;
//                if ($courriel != $prec['email']) {
//                    $is_new = 1;
//                } else {
//                    if ($prenom != $prec['prenom'] || $nom != $prec['nom']) {
//                        // adresse email identique mais personne différente
//                        $shuffle_letters = str_shuffle($letters);
//                        $datap['email'] = substr($shuffle_letters, 10, 20).'@mailerror.fpf';
//                        $datap['erreur_init_email'] = 1;
//                        $is_new = 1;
//                    }
//                }
//            } else {
//                // adresse email null
//                $shuffle_letters = str_shuffle($letters);
//                $datap['email'] = substr($shuffle_letters, 10, 20).'@mailerror.fpf';
//                $datap['erreur_init_email'] = 1;
//                if ($prenom != $prec['prenom'] || $nom != $prec['nom']) {
//                    $is_new = 1;
//                }
//            }
//
//            if ($is_new == 1) {
//                // on regarde si une personne existe avec cette adresse eamil
//                $personne = Personne::where('email', $datap['email'])->first();
//                if (!$personne) {
//                    $personne = Personne::create($datap);
//                }
//            }
////            else {
////
////            }
//
//            if ($personne) {
//                // on insère la relation utilisateur_personne
//                $datau = array('personne_id' => $personne->id, 'statut' => 0);
//                DB::table('utilisateurs')->where('id', $v->id)->update($datau);
//            }
//
//            // on recherche le max utilisateur avec la première partie de l'identifiant
//            $search = substr($v->identifiant, 0, 8);
//            $max = DB::table('utilisateurs')
//                ->where('statut', '!=', 10)
//                ->where('identifiant', 'like', $search.'%')
//                ->max('numeroutilisateur');
//            if ($max) {
//                $max++;
//            } else {
//                $max = 1;
//            }
//            $new_identifiant = $search.str_pad($max, 4, '0', STR_PAD_LEFT);
//
//            // on met à jour l'identifiant
//            DB::table('utilisateurs')->where('id', $v->id)->update(array('identifiant' => $new_identifiant, 'numeroutilisateur' => $max));
//
//            // on met à jour l'identifiant dans la table photos
//            DB::table('photos')->where('participants_id', $v->identifiant)->update(array('participants_id' => $new_identifiant));
//            DB::table('rphotos')->where('participants_id', $v->identifiant)->update(array('participants_id' => $new_identifiant));
//        }


    }
}
