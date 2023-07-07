<?php

namespace App\Console\Commands;

use App\Concern\Hash;
use App\Mail\CommandeMail;
use App\Mail\SendEmailReinitPassword;
use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class initPersonnes extends Command
{
    use Hash;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:personnes';

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
//        $pwd = $this->encodePwd('azertyui1236');
//        $link = 'https://google.fr';
//        Mail::to('contact@envolinfo.com')->send(new SendEmailReinitPassword($link));
//        dd($link);

        /*
         * statut 22 : participant openfed n'ayant jamais participé ==> a supprimer (35)
         * statut 12 : participant openfed ==> à conserver (1103)
         * statut 10 : plusieurs cas :
         *              avec identifiant de type 50- ==> participant à des formations ==> on ne conserve pas la carte mais uniquement les informations personne
         *              sinon ==> participant à des concours open ==> on conserve la carte mais on va les renommer
         * statut 6 : abonné gratuit => pas de carte, table abonnement
         * statut 5 : admin
         * statuts 0,1,2,3, et 4 : adhérents ou abonnés seuls
         * */

//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        DB::table('personnes')->truncate();
//        DB::table('abonnements')->truncate();
//        DB::table('adresse_personne')->truncate();
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//
//        $datau = array('personne_id' => null);
//        DB::table('utilisateurs')->update($datau);
//
//        Utilisateur::where('statut', 22)->delete();

        $utilisateurs = Utilisateur::whereNull('personne_id')->whereIn('statut', [0,1,2,3])->where('courriel', '<>', 'fpf@federation-photo.fr')->orderByDesc('courriel')->orderByDesc('adresses_id')->get();
//        $utilisateurs = Utilisateur::where('statut', 3)->orderByDesc('courriel')->orderByDesc('adresses_id')->limit(50)->get();
//        $utilisateurs = Utilisateur::where('statut', 5)->get();
//        $utilisateurs = Utilisateur::where('statut', 2)->orderBy('courriel')->orderByDesc('adresses_id')->limit(10)->get();
//        $utilisateurs = Utilisateur::where('statut', 12)->orderBy('courriel')->orderByDesc('adresses_id')->limit(10)->get();

        $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*";
        $statuts_adherents = [0,1,2,3,4];

        $config_saison = DB::table('configsaisons')->where('id', 1)->first();
        $numero_encours = $config_saison->numeroencours;

        $prec = array('email' => '', 'nom' => '', 'prenom' => '');
        $personne = null;
        foreach ($utilisateurs as $v) {
            $courriel = trim($v->courriel);
            $prenom = mb_convert_case(trim($v->prenom), MB_CASE_TITLE, "UTF-8");
            $nom = mb_strtoupper(trim($v->nom));

            $shuffle_letters = str_shuffle($letters);
//            $random_password = substr($shuffle_letters, 0, 8);
            $random_password = 'Az123456';
            $password = hash('sha512', $random_password);
            $datap = array(
                'nom' => $nom,
                'prenom' => $prenom,
                'sexe' => $v->sexe,
                'password' => $password,
                'premiere_connexion' => 1,
                'news'  => $v->news,
                'blacklist_date'  => $v->blacklist_date,
            );

            $is_new = 0;
            if ($courriel != '') {
                $datap['email'] = $courriel;
                if ($courriel != $prec['email']) {
                    $is_new = 1;
                } else {
                    if ($prenom != $prec['prenom'] || $nom != $prec['nom']) {
                        // adresse email identique mais personne différente
                        $datap['email'] = substr($shuffle_letters, 10, 20).'@mailerror.fpf';
                        $datap['erreur_init_email'] = 1;
                        $is_new = 1;
                    }
                }
            } else {
                // adresse email null
                $datap['email'] = substr($shuffle_letters, 10, 20).'@mailerror.fpf';
                $datap['erreur_init_email'] = 1;
                if ($prenom != $prec['prenom'] || $nom != $prec['nom']) {
                    $is_new = 1;
                }
            }

            if ($is_new == 1) {
                // on insère une nouvelle personne
                if ($v->datenaissance != '') {
                    $datap['datenaissance'] = $v->datenaissance;
                }
                if ($v->codeadmin != '') {
                    $datap['secure_code'] = $v->codeadmin;
                }
                if ($v->statut == 5) {
                    $datap['is_administratif'] = 1;
                }
                if ($v->statut == 6) {
                    $datap['is_abonne'] = 1;
                }
                if (in_array($v->statut, $statuts_adherents)) {
                    $datap['is_adherent'] = 1;
                }

                $adresse_user = null;
                if ($v->adresses_id != 0 && $v->adresses_id != 1) {
                    $adresse_user = Adresse::where('id', $v->adresses_id)->first();
                    if ($adresse_user) {
                        if ($adresse_user->telephonemobile != '') {
                            $datap['phone_mobile'] = $adresse_user->telephonemobile;
                        }
                    }
                }


                // on insère la personne
                $personne = Personne::create($datap);

                // on insère la relation adresse_personne
                if ($adresse_user) {
                    $dataa = array('adresse_id' => $adresse_user->id, 'personne_id' => $personne->id);
                    DB::table('adresse_personne')->insert($dataa);
                }
            } else {
                // l'utilisateur correspond à la personne précédente
            }

            if ($personne) {
                // on insère la relation utilisateur_personne
                $datau = array('personne_id' => $personne->id);
                DB::table('utilisateurs')->where('id', $v->id)->update($datau);
            }


            if ($v->numerofinabonnement != '') {
                // on insère l'abonnement
                $etat = ($v->numerofinabonnement < $numero_encours) ? 2 : 1;
                $dataa = array(
                    'personne_id' => $personne->id,
                    'debut' => $v->numerodebutabonnement ?? 0,
                    'fin' => $v->numerofinabonnement,
                    'etat' => $etat,
                );
                Abonnement::create($dataa);

                if ($etat == 1) {
                    // on met à jour la personne
                    $datap = array('is_abonne' => 1);
                    $personne->update($datap);
                }
            }



            if (!in_array($v->statut, $statuts_adherents)) {
                // on supprime l'utilisateur car non adhérent
//                DB::table('utilisateurs')->where('id', $v->id)->delete();
            }





            $prec['email'] = $courriel;
            $prec['nom'] = $nom;
            $prec['prenom'] = $prenom;
        }

    }
}
