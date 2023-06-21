<?php


namespace App\Concern;


use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Personne;

trait Tools
{
    public function registerAction($user = null, $action)
    {
        if (!$user || $user->personne) {
            return false;
        }
        $person = Personne::where('id', $user->personne)->first();
        if(!$person){
            return false;
        }
        $histo = Historique::create([
                'personne_id' => $user->personne,
                'utilisateur_id' => $user->utilisateur ?: 0,
                'type' => $action->type,
                'action' => $action->action,
            ]);
        if (!$histo){
            return false;
        }
        return true;
    }

    public function registerMail($user = null, $mail)
    {
        if (!$user || $user->personne) {
            return false;
        }
        $person = Personne::where('id', $user->personne)->first();
        if(!$person){
            return false;
        }
        $histoMails = Historiquemail::create([
            'personne_id' => $user->personne,
            'utilisateur_id' => $user->utilisateur ?: 0,
            'destinataire' => $mail->destinataire,
            'titre' => $mail->titre,
            'contenu' => $mail->contenu,
        ]);
        if (!$histoMails){
            return false;
        }
        return true;
    }
}
