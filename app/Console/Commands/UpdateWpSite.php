<?php

namespace App\Console\Commands;

use App\Models\Activite;
use App\Models\Club;
use App\Models\Equipement;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateWpSite extends Command
{
    /**
     * Mise à jour quotidienne des clubs sur le site wordpress
     *
     * @var string
     */
    protected $signature = 'update:wpsite';

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
        // on récupère tous les clubs validés (statut = 2)
        $clubs = Club::where('statut', 2)->get();
        foreach ($clubs as $club) {
            $numero_club = $club->numero;
            // pour chaque club, on récupère les activités et les quipement
            $activites = Activite::join('activitesclubs', 'activites.id', '=', 'activitesclubs.activites_id')
                ->where('activitesclubs.clubs_id', $club->id)
                ->get();
            $chaine_activites = '';
            if (sizeof($activites) > 0) {
                $chaine_activites .= '<ul>';
                foreach ($activites as $activite) {
                    $chaine_activites .= '<li>'.$activite->libelle.'</li>';
                }
                $chaine_activites .= '</ul>';
            }

            $equipements = Equipement::join('equipementsclubs', 'equipements.id', '=', 'equipementsclubs.equipements_id')
                ->where('equipementsclubs.clubs_id', $club->id)
                ->get();
            $chaine_equipements = '';
            if (sizeof($equipements) > 0) {
                $chaine_equipements .= '<ul>';
                foreach ($equipements as $equipement) {
                    $chaine_equipements .= '<li>'.$equipement->libelle.'</li>';
                }
                $chaine_equipements .= '</ul>';
            }

            // on récupère les fonctions club
            $fonctions = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
                ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
                ->where('utilisateurs.clubs_id', $club->id)
                ->whereNotNull('utilisateurs.personne_id')
                ->whereIn('fonctionsutilisateurs.fonctions_id', [94,95,96,97])
                ->get();

            $wp_club = DB::connection('mysqlwp')->select("SELECT P.ID, M.meta_value FROM wp_posts P, wp_postmeta M WHERE M.post_id = P.id AND P.post_type = 'club-ur' AND M.meta_key = 'ptb_numero' AND M.meta_value = $numero_club LIMIT 1");
            if ($wp_club) {
                // on met à jour le club
                $post_id = $wp_club[0]->ID;

                $metas = DB::connection('mysqlwp')->select("SELECT * FROM wp_postmeta WHERE post_id = $post_id");

                $adresse_club = addslashes($club->adresse->libelle1.' '.$club->adresse->libelle2);
                if ($club->web != '') {
                    if (strpos($club->web, ":") !== false) {
                        $tabweb = explode('://', $club->web);
                        if (isset($tabweb[1])) {
                            $chaineweb = $tabweb[1];
                        }
                        else {
                            $chaineweb = '';
                        }
                    }
                    else {
                        $chaineweb = $club->web;
                    }
                    $lgchaineweb = strlen($chaineweb);
                    $lgchaineweb2 = $lgchaineweb + 8;
                    $chaine_site = 'a:2:{i:0;s:'.$lgchaineweb.':"'.$chaineweb.'";i:1;s:'.$lgchaineweb2.':"https://'.$chaineweb.'";}';
                } else {
                    $chaine_site = '';
                }
                $metakeys = array(
                    'ptb_text_3' => $adresse_club,
                    'ptb_email' => $club->courriel,
                    'ptb_telephone_club' => $club->adresse->telephonedomicile,
                    'ptb_telephone_mobile' => $club->adresse->telephonemobile,
                    'ptb_site_internet' => $chaine_site,
                    'ptb_president' => '',
                    'ptb_tresorier' => '',
                    'ptb_secretaire' => '',
                    'ptb_contact' => '',
                    'ptb_activites' => $chaine_activites,
                    'ptb_equipements' => $chaine_equipements,
                    'ptb_reunion_frequence' => $club->frequencereunions,
                    'ptb_reunion_horaires' => addslashes($club->horairesreunions),
                    'ptb_commentaire' => addslashes($club->reunions)
                );
                foreach ($fonctions as $fonction) {
                    $name = addslashes(ucfirst(strtolower($fonction->personne->prenom)).' '.ucfirst(strtolower($fonction->personne->nom)));
                    switch ($fonction->fonctions_id) {
                        case 94: $metakeys['ptb_president'] = $name; break;
                        case 95: $metakeys['ptb_tresorier'] = $name; break;
                        case 96: $metakeys['ptb_secretaire'] = $name; break;
                        case 97: $metakeys['ptb_contact'] = $name; break;
                        default: break;
                    }
                }
                foreach ($metas as $meta) {
                    if (array_key_exists($meta->meta_key, $metakeys)) {
                        if ($meta->meta_value != $metakeys[$meta->meta_key]) {
                            $meta_id = $meta->meta_id;
                            $new_value = addslashes($metakeys[$meta->meta_key]);
                            DB::connection('mysqlwp')->statement("UPDATE wp_postmeta SET meta_value = '$new_value' WHERE meta_id = $meta_id LIMIT 1");
                        }
                    }
                }
            } else {
                // on insère le club
                $now = date('Y-m-d H:i:s');
                $now_gmt = gmdate('Y-m-d H:i:s');
                $nomclub = addslashes(trim($club->nom));
                $nomclub2 = str_replace('-', '', trim($club->nom));
                $nomclub3 = str_replace('.', '', $nomclub2);
                $postname1 = str_replace(' ', '-', strtolower($nomclub3));
                $postname2 = str_replace("'", '', $postname1);
                $postname2 = Str::slug($postname2);
                $postname = iconv('UTF-8', 'ISO-8859-15//TRANSLIT//IGNORE', utf8_decode(str_replace('--', '-', $postname2)));
                $ins_wp_posts = "INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, comment_status,
                      ping_status, post_name, post_modified, post_modified_gmt, to_ping, pinged, post_content_filtered, guid, post_type, post_mime_type)
                        VALUES (1, '".$now."', '".$now_gmt."', '', '".$nomclub."', '', 'closed', 'closed', '".$postname."', '".$now."',
                        '".$now_gmt."', '', '', '', '', 'club-ur', '')";
                DB::connection('mysqlwp')->statement($ins_wp_posts);

                $max_post = DB::connection('mysqlwp')->select("SELECT MAX(ID) as max FROM wp_posts");
                $post_id = $max_post[0]->max;

                $guid = "https://federation-photo.fr/?post_type=club-ur&#038;p=".$post_id;
                DB::connection('mysqlwp')->statement("UPDATE wp_posts SET guid = '$guid' WHERE ID = $post_id LIMIT 1");

                $codepostal = str_pad($club->adresse->codepostal, 5, '0', STR_PAD_LEFT);
                $adresse_club = addslashes($club->adresse->libelle1.' '.$club->adresse->libelle2);
                if ($club->web != '') {
                    if (str_contains($club->web, ":")) {
                        $tabweb = explode('://', $club->web);
                        if (isset($tabweb[1])) {
                            $chaineweb = $tabweb[1];
                        }
                        else {
                            $chaineweb = '';
                        }
                    }
                    else {
                        $chaineweb = $club->web;
                    }
                    $lgchaineweb = strlen($chaineweb);
                    $lgchaineweb2 = $lgchaineweb + 8;
                    $chaine_site = 'a:2:{i:0;s:'.$lgchaineweb.':"'.$chaineweb.'";i:1;s:'.$lgchaineweb2.':"https://'.$chaineweb.'";}';
                } else {
                    $chaine_site = '';
                }

                $metakeys = array(
                    'ptb_departement'       => substr($codepostal, 0, 2),
                    'ptb_text_3'            => $adresse_club,
                    'ptb_ville'             => strtoupper(addslashes($club->adresse->ville)),
                    'ptb_code_postal'       => $codepostal,
                    'ptb_email'             => $club->courriel,
                    'ptb_reunion_frequence' => $club->frequencereunions,
                    'ptb_reunion_horaires'  => addslashes($club->horairesreunions),
                    'ptb_commentaire'       => addslashes($club->reunions),
                    'ptb_activites'         => $chaine_activites,
                    'ptb_equipements'       => $chaine_equipements,
                    '_edit_lock'            => '',
                    '_edit_last'            => 1,
                    'ptb_numero'            => $club->numero,
                    '_yoast_wpseo_primary_ur' => '',
                    '_yoast_wpseo_content_score'    => '30',
                    'ptb_telephone_club'    => $club->adresse->telephonedomicile,
                    'ptb_telephone_mobile'  => $club->adresse->telephonemobile,
                    'ptb_site_internet'     => $chaine_site
                );
                foreach ($fonctions as $fonction) {
                    $name = addslashes(ucfirst(strtolower($fonction->personne->prenom)).' '.ucfirst(strtolower($fonction->personne->nom)));
                    switch ($fonction->fonctions_id) {
                        case 94: $metakeys['ptb_president'] = $name; break;
                        case 95: $metakeys['ptb_tresorier'] = $name; break;
                        case 96: $metakeys['ptb_secretaire'] = $name; break;
                        case 97: $metakeys['ptb_contact'] = $name; break;
                        default: break;
                    }
                }
                foreach ($metakeys as $key => $meta) {
                    DB::connection('mysqlwp')->statement("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES ($post_id, '$key', '$meta')");
                }

                // insertion cu club dans l'ur
                $ur_id = str_pad($club->urs_id, 2, '0', STR_PAD_LEFT);
                $wp_ur = DB::connection('mysqlwp')->select("SELECT ID FROM wp_posts WHERE post_name LIKE '$ur_id%' AND post_type = 'ur'");
                if ($wp_ur) {
                    $ur_post_id = $wp_ur[0]->ID;
                    $meta_ur = DB::connection('mysqlwp')->select("SELECT meta_value, meta_id FROM wp_postmeta WHERE post_id = $ur_post_id AND meta_key = 'ptb_clubs'");
                    if ($meta_ur) {
                        if ($meta_ur[0]->meta_value == '') {
//                            $chainemeta = $post_id;
                            $chaine_club = $post_id;
                        }
                        else {
//                            $chaine = 'a:2:{s:7:"relType";s:1:"1";s:3:"ids";s:422:"155286, 155287, 155288, 155290, 155291, 155292, 155293, 155294, 155295, 155296, 155297, 155298, 155300, 155301, 155302, 155303, 155305, 155306, 155307, 155308, 155309, 155310, 155306, 155293, 155294, 155300, 155307, 155305, 155303, 155294, 155295, 155309, 155302, 155298, 155288, 155296, 155297, 155291, 155287, 155290, 155286, 155292, 155292, 155292, 155310, 155308, 155301, 173817, 174129, 176890, 176976, 182549, 183414";}';
                            $tab = explode('"', $meta_ur[0]->meta_value);
                            $chaine_club = $tab[7];
                            $chaine_club .= ', '.$post_id;
                        }
                        $len_chaine_club = strlen($chaine_club);
                        $chaine_meta = 'a:2:{s:7:"relType";s:1:"1";s:3:"ids";s:'.$len_chaine_club.':"'.$chaine_club.'";}';
                        $meta_id = $meta_ur[0]->meta_id;

//                        a:2:{s:7:"relType";s:1:"1";s:3:"ids";s:422:"155286, 155287, 155288, 155290, 155291, 155292, 155293, 155294, 155295, 155296, 155297, 155298, 155300, 155301, 155302, 155303, 155305, 155306, 155307, 155308, 155309, 155310, 155306, 155293, 155294, 155300, 155307, 155305, 155303, 155294, 155295, 155309, 155302, 155298, 155288, 155296, 155297, 155291, 155287, 155290, 155286, 155292, 155292, 155292, 155310, 155308, 155301, 173817, 174129, 176890, 176976, 182549, 183414";}
                        DB::connection('mysqlwp')->statement("UPDATE wp_postmeta SET meta_value = '$chaine_meta' WHERE meta_id = $meta_id LIMIT 1");
                    }
                }

            }
        }
    }
}
