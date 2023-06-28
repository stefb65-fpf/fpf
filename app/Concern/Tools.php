<?php


namespace App\Concern;


use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Personne;

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
        if(!$personne){
            return false;
        }
        $histo = Historique::create([
                'personne_id' => $personne_id,
                'utilisateur_id' => $utilisateur_id ?: null,
                'type' => $type,
                'action' => $action,
            ]);
        if (!$histo){
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
        if(!$personne){
            return false;
        }
        $histoMails = Historiquemail::create([
            'personne_id' => $personne_id,
            'utilisateur_id' => $utilisateur_id ?: null,
            'destinataire' => $mail->destinataire,
            'titre' => $mail->titre,
            'contenu' => $mail->contenu,
        ]);
        if (!$histoMails){
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
}
