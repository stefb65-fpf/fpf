<?php


namespace App\Concern;


use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Personne;
use Illuminate\Database\Query\Builder;

trait Tools
{
    /**
     * enregistre l'action utilisateur dans la table historique
     * @param $personne_id integer
     * @param $type integer (0: gestion profil, 1: abonnement adhésion, 2: formation, 3: connexion inscription
     * @param $action string
     * @param $utilisateur_id integer|null
     * @return bool
     */
    public function registerAction(int $personne_id, int $type, string $action, int $utilisateur_id = null)
    {
        if (!$personne_id || !$type || !$action) {
            return false;
        }
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return false;
        }
        $histo = Historique::create([
            'personne_id' => $personne_id,
            'utilisateur_id' => $utilisateur_id ?: null,
            'type' => $type,
            'action' => $action,
        ]);
        if (!$histo) {
            return false;
        }
        return true;
    }

    /**
     * @param null $user
     * @param $mail
     * @return bool
     */
    public function registerMail(int $personne_id, $mail, int $utilisateur_id = null)
    {
        if (!$personne_id || !$mail) {
            return false;
        }
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return false;
        }
        $histoMails = Historiquemail::create([
            'personne_id' => $personne_id,
            'utilisateur_id' => $utilisateur_id ?: null,
            'destinataire' => $mail->destinataire,
            'titre' => $mail->titre,
            'contenu' => $mail->contenu,
        ]);
        if (!$histoMails) {
            return false;
        }
        return true;
    }

    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function format_phone_number_visual($string)
    {
        $phone_number = $string;
        if(strlen($string)){
            //si $string commence par un 0 et contient des espaces, on les enleve
            $phone_number = ltrim(str_replace( " ","",$string),"0");
            //séparer avant et après le "."
            $tab = explode('.', $string);
            if (sizeof($tab) > 1) {
                $phone_number = $tab[1];
            }
//        ajouter des espaces
            $phone_number = chunk_split("0" . $phone_number, 2, ' ');
        }
        return $phone_number;
    }

    public function format_phone_number_callable($string)
    {
        $phone_number = $string;
        if(strlen($string)){
            //si $string commence par un 0 et contient des espaces, on les enleve
            $phone_number = ltrim(str_replace( " ","",$string),"0");
            //enlever le '.'
            $phone_number = str_replace('.', "",$string);
        }
        return $phone_number;
    }
    public function format_web_url($string)
    {
        $array = ['https://', 'www','http://'];
        if(strlen($string)){
            $string = "https://".ltrim(str_replace($array, "",$string),".");
        }
        return $string;
    }
    public function format_fixe_for_base($number, $indicatif){
        if ($number) {
            $number = str_replace(" ", "", $number);
            $number= ltrim($number, '0');
            $number = '+' . $indicatif . '.' . $number;
        }
        return $number;
    }
    public function format_mobile_for_base($number){
        if ($number) {
            $first_two_numbers = substr($number, 0, 2);
            if ($first_two_numbers == "06" || $first_two_numbers == "07") {
                // remove "0" and add "+33."
                $number = str_replace(" ", "", $number);
                $number = ltrim($number, '0');
                $number = '+33.' . $number;
            }
        }
return $number;
    }
    public function getClubsByTerm($term,$query){
        if (is_numeric($term)) {
            $query =$query->where('numero', 'LIKE', '%'.$term.'%')->get();
        } else {
            $query = $query->where('nom', 'LIKE','%'. $term.'%')->get();
        }
        return $query;
    }
    public function getPersonsByTerm($term,$query){

        $query = $query->where(
            function($query) use ($term){
                $query->where('utilisateurs.identifiant', 'LIKE', '%'.$term.'%')
                    ->orWhere('personnes.email', 'LIKE', '%'.$term.'%')
                   ->orWhere('personnes.nom', 'LIKE', '%'.$term.'%')
                  ->orWhere('personnes.prenom', 'LIKE', '%'.$term.'%');
            }
        );

        return $query;
    }
}
