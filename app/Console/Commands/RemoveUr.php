<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Photo;
use App\Models\Rcompetition;
use App\Models\Rphoto;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveUr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:ur';

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
        $prod = 1;
        $ur_init = 25;
        $ur_final = 14;

        // on récupère tous les clubs de l'UR 25
        $clubs = Club::where('urs_id', $ur_init)->get();
        $file_log = '/home/vhosts/fpf.federation-photo.fr/htdocs/removeur25.log';
        // on ouvre le fichier de log
        $log = fopen($file_log, 'w');

        $data_ur = ['urs_id' => $ur_final];
        foreach ($clubs as $club) {
            // on affiche les infos
            fwrite($log, "****************** TRAITEMENT CLUB : " . $club->numero . " - " . $club->nom . "\n");
            // on change l'UR de chaque club
            if ($prod == 1) {
                $club->update($data_ur);
            }

            // on récupère les utilisateurs appartenant au club
            $users = Utilisateur::where('clubs_id', $club->id)->get();
            if (sizeof($users) > 0) {
                foreach ($users as $user) {
                    $this->trait_user($user, $prod, $ur_final, $log);
                    $this->trait_fonctions($user, $prod, $log);
                }
            }
        }

        // on cherche tous les utilisateuers individuels de l'UR 25, donc avec clubs_id à null
//        $users = Utilisateur::whereNull('clubs_id')->where('urs_id', $ur_init)->get();
//        foreach ($users as $user) {
//            $this->trait_user($user, $prod, $ur_final, $log);
//            $this->trait_fonctions($user, $prod, $log);
//        }
        fclose($log);

    }

    protected function trait_user($user, $prod, $ur_final, $log) {
        // on affiche les infos
        fwrite($log, "**** Traitement utilisateur : " . $user->nom . " " . $user->prenom . "\n");
        // on change l'UR et l'identifiant de chaque utilisateur
        $identifiant = $ur_final.'-'.substr($user->identifiant, 3);
        fwrite($log, "ancien identifiant : " . $user->identifiant . "\n");
        fwrite($log, "nouvel identifiant : " . $identifiant . "\n");

        // TODO tester si le nouvel identifiant n'existe pas
        $exist = Utilisateur::where('identifiant', $identifiant)->first();
        if ($exist) {
            fwrite($log, "ERREUR : l'identifiant existe déjà\n");
        } else {
            // on parcourt la table photos en regardant si le champ participant_id est égal à l'identifiant de l'utilisateur
            $photos = Photo::where('participants_id', $user->identifiant)->get();
            if (sizeof($photos) > 0) {
                foreach ($photos as $photo) {
                    // on récupère la compétition de la photo
                    $competition = Competition::where('id', $photo->competitions_id)->first();
                    // TODO traiter le fait que ça peut être des ouevres photos ou dossier auteur ou xls
                    if ($competition->nature != 0) {
                        echo "ERREUR : la compétition $competition->id n'est pas une compétition photo. Nature $competition->nature \n";
                    }

                    // on affiche les infos
                    fwrite($log, "** Traitement photo : " . $photo->id . "\n");
                    // on change l'ean de chaque photo
                    $new_ean = $ur_final . substr($photo->ean, 2);
                    // on change l'identifiant de chaque photo
                    $data_photo = ['participants_id' => $identifiant, 'ean' => $new_ean];
                    fwrite($log, "ancien ean : " . $photo->ean . "\n");
                    fwrite($log, "nouvel ean : " . $new_ean . "\n");
                    fwrite($log, "ancien participant_id : " . $photo->participant_id . "\n");
                    fwrite($log, "nouveau participant_id : " . $identifiant . "\n");
                    if ($prod == 1) {
                        $photo->update($data_photo);
                    }

                    if ($competition) {
                        // on renomme le fichier photo sur le serveur
                        $path = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/national/' . $competition->saison . '/compet' . $competition->numero . '/';
                        if ($competition->nature == 0) {
                            $path_thumb = $path . 'thumbs/';
                            $old_file = $path . $photo->ean . '.jpg';
                            $new_file = $path . $new_ean . '.jpg';
                            $old_file_thumb = $path_thumb . $photo->ean . '.jpg';
                            $new_file_thumb = $path_thumb . $new_ean . '.jpg';
                            fwrite($log, "ancien fichier : " . $old_file . "\n");
                            if (file_exists($old_file)) {
                                fwrite($log, "fichier existant\n");
                                fwrite($log, "nouveau fichier : " . $new_file . "\n");
                            } else {
                                fwrite($log, "fichier inexistant\n");
                            }

                            fwrite($log, "ancien fichier thumb : " . $old_file_thumb . "\n");
                            if (file_exists($old_file_thumb)) {
                                fwrite($log, "fichier thumb existant\n");
                                fwrite($log, "nouveau fichier thumb : " . $new_file_thumb . "\n");
                            } else {
                                fwrite($log, "fichier thumb inexistant\n");
                            }

                            if ($prod == 1) {
                                if (file_exists($old_file)) {
                                    rename($old_file, $new_file);
                                }
                                if (file_exists($old_file_thumb)) {
                                    rename($old_file_thumb, $new_file_thumb);
                                }
                            }
                        }
                        if ($competition->nature == 1 || $competition->nature == 3) {
                            $path .= $photo->ean.'/';
                            $path_thumb = $path . 'thumbs/';
                            // on parcourt tout le répertoire et on renomme les images
                            if (!file_exists($path)) {
                                fwrite($log, "ERREUR : le répertoire $path n'existe pas\n");
                            } else {
                                $files = scandir($path);
                                foreach ($files as $file) {
                                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                                    if ($ext != 'jpg' && $ext != 'pte') {
                                        fwrite($log, "ERREUR : l'extension $ext n'est pas jpg ou pte\n");
                                    }
                                    if ($file != '.' && $file != '..' && in_array($ext, ['jpg', 'pte'])) {
                                        $old_file = $path . $file;
                                        $new_file = $path . $ur_final . substr($file, 2);
                                        fwrite($log, "ancien fichier auteur : " . $old_file . "\n");
                                        if (file_exists($old_file)) {
                                            fwrite($log, "fichier existant\n");
                                            fwrite($log, "nouveau fichier auteur : " . $new_file . "\n");
                                        } else {
                                            fwrite($log, "fichier inexistant\n");
                                        }

                                        $old_file_thumb = $path_thumb . $file;
                                        $new_file_thumb = $path_thumb . $ur_final . substr($file, 2);
                                        fwrite($log, "ancien fichier auteur thumb : " . $old_file_thumb . "\n");
                                        if (file_exists($old_file_thumb)) {
                                            fwrite($log, "fichier thumb existant\n");
                                            fwrite($log, "nouveau fichier auteur thumb : " . $new_file_thumb . "\n");
                                        } else {
                                            fwrite($log, "fichier thumb inexistant\n");
                                        }

                                        if ($prod == 1) {
                                            if (file_exists($old_file)) {
                                                rename($old_file, $new_file);
                                            }
                                            if (file_exists($old_file_thumb)) {
                                                rename($old_file_thumb, $new_file_thumb);
                                            }
                                        }
                                    }
                                }

                                // on renomme le répertoire $path
                                $new_path = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/national/' . $competition->saison . '/compet' . $competition->numero . '/' . $new_ean . '/';
                                fwrite($log, "ancien répertoire auteur : " . $path . "\n");
                                fwrite($log, "nouveau répertoire auteur : " . $new_path . "\n");
                                if ($prod == 1) {
                                    rename($path, $new_path);
                                }
                            }
                        }
                    }
                }
            }
            // on parcourt la table rphotos en regardant si le champ participant_id est égal à l'identifiant de l'utilisateur
            $rphotos = Rphoto::where('participants_id', $user->identifiant)->get();
            if (sizeof($rphotos) > 0) {
                foreach ($rphotos as $rphoto) {
                    // on récupère la compétition de la photo
                    $competition = Rcompetition::where('id', $rphoto->competitions_id)->first();
                    if ($competition->nature != 0) {
                        echo "ERREUR : la compétition régionale $competition->id n'est pas une compétition photo. Nature $competition->nature \n";
                    }

                    // on affiche les infos
                    fwrite($log, "** Traitement rphoto : " . $rphoto->id . "\n");
                    // on change l'ean de chaque rphoto
                    $new_ean = $ur_final . substr($rphoto->ean, 2);
                    // on change l'identifiant de chaque rphoto
                    $data_rphoto = ['participants_id' => $identifiant, 'ean' => $new_ean];
                    fwrite($log, "ancien ean : " . $rphoto->ean . "\n");
                    fwrite($log, "nouvel ean : " . $new_ean . "\n");
                    fwrite($log, "ancien participant_id : " . $rphoto->participants_id . "\n");
                    fwrite($log, "nouveau participant_id : " . $identifiant . "\n");
                    if ($prod == 1) {
                        $rphoto->update($data_rphoto);
                    }

                    if ($competition) {
                        // on renomme le fichier photo sur le serveur
                        $urpad = str_pad($competition->urs_id, 2, '0', STR_PAD_LEFT);
                        $path = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/regional/' . $competition->saison . '/UR' . $urpad . '/compet' . $competition->numero . '/';
                        if ($competition->nature == 0) {
                            $path_thumb = $path . 'thumbs/';
                            $old_file = $path . $rphoto->ean . '.jpg';
                            $new_file = $path . $new_ean . '.jpg';
                            $old_file_thumb = $path_thumb . $rphoto->ean . '.jpg';
                            $new_file_thumb = $path_thumb . $new_ean . '.jpg';
                            fwrite($log, "ancien fichier : " . $old_file . "\n");
                            if (file_exists($old_file)) {
                                fwrite($log, "fichier existant\n");
                                fwrite($log, "nouveau fichier : " . $new_file . "\n");
                            } else {
                                fwrite($log, "fichier inexistant\n");
                            }

                            fwrite($log, "ancien fichier thumb : " . $old_file_thumb . "\n");
                            if (file_exists($old_file_thumb)) {
                                fwrite($log, "fichier thumb existant\n");
                                fwrite($log, "nouveau fichier thumb : " . $new_file_thumb . "\n");
                            } else {
                                fwrite($log, "fichier thumb inexistant\n");
                            }

                            if ($prod == 1) {
                                if (file_exists($old_file)) {
                                    rename($old_file, $new_file);
                                }
                                if (file_exists($old_file_thumb)) {
                                    rename($old_file_thumb, $new_file_thumb);
                                }
                            }
                        }

                        if ($competition->nature == 1 || $competition->nature == 3) {
                            $path .= $photo->ean.'/';
                            $path_thumb = $path . 'thumbs/';
                            // on parcourt tout le répertoire et on renomme les images
                            if (!file_exists($path)) {
                                fwrite($log, "ERREUR : le répertoire $path n'existe pas\n");
                            } else {
                                $files = scandir($path);
                                foreach ($files as $file) {
                                    // on vérifie que l'extension est jpg ou pte
                                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                                    if ($ext != 'jpg' && $ext != 'pte') {
                                        fwrite($log, "ERREUR : l'extension $ext n'est pas jpg ou pte\n");
                                    }
                                    if ($file != '.' && $file != '..' && in_array($ext, ['jpg', 'pte'])) {
                                        $old_file = $path . $file;
                                        $new_file = $path . $ur_final . substr($file, 2);
                                        fwrite($log, "ancien fichier auteur : " . $old_file . "\n");
                                        if (file_exists($old_file)) {
                                            fwrite($log, "fichier existant\n");
                                            fwrite($log, "nouveau fichier auteur : " . $new_file . "\n");
                                        } else {
                                            fwrite($log, "fichier inexistant\n");
                                        }

                                        $old_file_thumb = $path_thumb . $file;
                                        $new_file_thumb = $path_thumb . $ur_final . substr($file, 2);
                                        fwrite($log, "ancien fichier auteur thumb : " . $old_file_thumb . "\n");
                                        if (file_exists($old_file_thumb)) {
                                            fwrite($log, "fichier thumb existant\n");
                                            fwrite($log, "nouveau fichier auteur thumb : " . $new_file_thumb . "\n");
                                        } else {
                                            fwrite($log, "fichier thumb inexistant\n");
                                        }

                                        if ($prod == 1) {
                                            if (file_exists($old_file)) {
                                                rename($old_file, $new_file);
                                            }
                                            if (file_exists($old_file_thumb)) {
                                                rename($old_file_thumb, $new_file_thumb);
                                            }
                                        }
                                    }
                                }

                                // on renomme le répertoire $path
                                $new_path = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/regional/' . $competition->saison . '/UR' . $urpad . '/compet' . $competition->numero . '/' . $new_ean . '/';
                                fwrite($log, "ancien répertoire auteur : " . $path . "\n");
                                fwrite($log, "nouveau répertoire auteur : " . $new_path . "\n");
                                if ($prod == 1) {
                                    rename($path, $new_path);
                                }
                            }
                        }
                    }
                }
            }

            if ($prod == 1) {
                $data_user = ['identifiant' => $identifiant, 'urs_id' => $ur_final];
                $user->update($data_user);
            }
        }
    }

    protected function trait_fonctions($user, $prod, $log) {
        // on récupère les fonctions de l'utilisateur
        $fonctions = DB::table('fonctionsutilisateurs')
            ->join('fonctions', 'fonctionsutilisateurs.fonctions_id', '=', 'fonctions.id')
            ->where('fonctionsutilisateurs.utilisateurs_id', $user->id)
            ->where('fonctions.instance', 2)
            ->selectRaw('fonctions.id')
            ->get();
        if (sizeof($fonctions) > 0) {
            foreach ($fonctions as $fonction) {
                // on affiche les infos
                fwrite($log, "** Suppression fonction : " . $fonction->id . "\n");
                // on supprime la fonction pour l'utilisateur
                if ($prod == 1) {
                    DB::table('fonctionsutilisateurs')->where('utilisateurs_id', $user->id)->where('fonctions_id', $fonction->id)->delete();
                }
            }
        }
    }
}
