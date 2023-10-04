<?php

namespace App\Console\Commands;

use App\Concern\Api;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Mail\ConfirmationInscriptionFormation;
use App\Models\Inscrit;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Souscription;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckBridge extends Command
{
    use Api;
    use Tools;
    use Invoice;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:bridge';

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
        $reglements = Reglement::where('statut', 0)->whereNotNull('bridge_id')->get();
        foreach($reglements as $reglement) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $reglement->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);

            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement a été effectué
                    if ($this->saveReglement($reglement)) {
                        $data =array('statut' => 1, 'bridge_id' => null, 'bridge_link' => null,
                            'numerocheque' => 'Bridge '.$reglement->bridge_id, 'dateenregistrement' => date('Y-m-d H:i:s'));
                        $reglement->update($data);

                        $this->saveInvoiceForReglement($reglement);
                    }
                }

                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    // le paiement n'a pas été effectué
                    $data =array('bridge_id' => null, 'bridge_link' => null);
                    $reglement->update($data);
                }
            }
        }


        $personnes = Personne::where('attente_paiement', 1)->whereNotNull('bridge_id')->get();
        foreach ($personnes as $personne) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $personne->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);

            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    // le paiement n'a pas été effectué
                    if ($personne->action_paiement == 'ADD_INDIVIDUEL' || $personne->action_paiement == 'ADD_ABONNEMENT') {
                        // on supprime la personne
                        $this->deleteWpUser($personne->email);
                        $personne->delete();
                    }
                }

                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement a été effectué
                    if ($personne->action_paiement == 'ADD_INDIVIDUEL' || $personne->action_paiement == 'ADD_ABONNEMENT') {
                        // on crée l'adhérent
                        if ($personne->action_paiement == 'ADD_INDIVIDUEL') {
                            $description = "Adhésion individuelle à la FPF";
                        } else {
                            $description = "Abonnement à la revue France Photo";
                        }
                        list($code, $reglement) = $this->saveNewPersonne($personne, 'Bridge');


                        $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                        $this->createAndSendInvoice($datai);
                    }

                    if ($personne->action_paiement == 'ADD_INDIVIDUEL_CARD') {
                        // on crée la carte
                        list($code, $reglement) = $this->saveNewCard($personne, 'Bridge');

                        $description = "Adhésion individuelle à la FPF";
                        $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                        $this->createAndSendInvoice($datai);
                    }

                    if ($personne->action_paiement == 'ADD_NEW_ABO') {
                        // on crée la carte
                        list($code, $reglement) = $this->saveNewAbo($personne, 'Monext');

                        $description = "Abonnement individuel à la revue France Photo";
                        $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                        $this->createAndSendInvoice($datai);
                    }
                }
            }
        }


        $souscriptions = Souscription::where('statut', 0)->whereNotNull('bridge_id')->get();
        foreach($souscriptions as $souscription) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $souscription->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);

            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement a été effectué
                    $data =array('statut' => 1, 'bridge_id' => null, 'bridge_link' => null,
                        'ref_reglement' => 'Bridge '.$souscription->bridge_id);
                    $souscription->update($data);

                    if ($souscription->personne_id) {
                        $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                        $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'personne_id' => $souscription->personne_id];
                        $this->createAndSendInvoice($datai);
                    } else {
                        if ($souscription->clubs_id) {
                            $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                            $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'club_id' => $souscription->clubs_id];
                            $this->createAndSendInvoice($datai);
                        }
                    }
                }

                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    // le paiement n'a pas été effectué
                    $souscription->delete();
                }
            }
        }

        $inscrits = Inscrit::where('attente_paiement', 1)->whereNotNull('bridge_id')->get();
        foreach ($inscrits as $inscrit) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $inscrit->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);
            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement de l'inscription a été effectué
                    $data = ['attente_paiement' => 0, 'status' => 1];
                    $inscrit->update($data);
                    $formation = $inscrit->session->formation;

                    $email = $inscrit->personne->email;
                    $mailSent = Mail::to($email)->send(new ConfirmationInscriptionFormation($inscrit->session));
                    $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                    $sujet = "FPF // Inscription à la formation $formation->name";
                    $mail = new \stdClass();
                    $mail->titre = $sujet;
                    $mail->destinataire = $email;
                    $mail->contenu = $htmlContent;
                    $this->registerMail($inscrit->personne->id, $mail);

                    $sujet = "Inscription à la formation $formation->name";
                    $this->registerAction($inscrit->personne->id, 2, $sujet);

                    $description = "Inscription à la formation ".$inscrit->session->formation->name;
                    $ref = 'FORMATION-'.$inscrit->personne_id.'-'.$inscrit->session_id;
                    $datai = ['reference' => $ref, 'description' => $description, 'montant' => $inscrit->session->price, 'personne_id' => $inscrit->personne->id];
                    $this->createAndSendInvoice($datai);
                }
                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    $inscrit->delete();
                }
            }
        }
    }
}
