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
     * @param $type integer
     * @param $action string
     * @param $utilisateur_id integer|null
     * @return bool
     */
    public function registerAction(int $personne_id, int $type, string $action, int $utilisateur_id = null)
    {
        if (!$personne_id || !$type || !$action) {
            return false;
        }
        $person = Personne::where('id', $personne_id)->first();
        if(!$person){
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
     * enregistre le mail envoyé à l'utilisateur dans la table historiquemail
     * @param $personne_id integer
     * @param $destinataire integer
     * @param $titre integer
     * @param $contenu string
     * @param $utilisateur_id integer|null
     * @return bool
     */
    public function registerMail($personne_id, $destinataire,  $titre, $contenu, $utilisateur_id = null)
    {
        if (!$personne_id || !$destinataire || !$titre || !$contenu) {
            return false;
        }
        $person = Personne::where('id', $personne_id)->first();
        if(!$person){
            return false;
        }
        $histoMails = Historiquemail::create([
            'personne_id' =>$personne_id,
            'utilisateur_id' => $utilisateur_id ?: null,
            'destinataire' => $destinataire,
            'titre' => $titre,
            'contenu' => $contenu,
        ]);
        if (!$histoMails){
            return false;
        }
        return true;
    }
}
