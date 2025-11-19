<?php

namespace App\Console\Commands;

use App\Concern\Api;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Mail\ConfirmationInscriptionDept;
use App\Mail\ConfirmationInscriptionFormation;
use App\Mail\ConfirmationPriseEnChargeSession;
use App\Models\Club;
use App\Models\Inscrit;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Session;
use App\Models\Souscription;
use App\Models\Ur;
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

                        if ($reglement->clubs_id) {
                            $club = Club::where('id', $reglement->clubs_id)->first();
                            if ($club) {
                                if ($club->creance > 0) {
                                    $montant_creance_utilisee = $reglement->montant - $reglement->montant_paye;
                                    $new_creance = $club->creance - $montant_creance_utilisee > 0 ? $club->creance - $montant_creance_utilisee : 0;
                                    $club->update(['creance' => $new_creance]);
                                }
                            }
                        }

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
                        $this->saveReglementEvents($reglement->id);

                        $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                        $this->createAndSendInvoice($datai);
                    }

                    if ($personne->action_paiement == 'ADD_INDIVIDUEL_CARD') {
                        // on crée la carte
                        list($code, $reglement) = $this->saveNewCard($personne, 'Bridge');
                        $this->saveReglementEvents($reglement->id);

                        $description = "Adhésion individuelle à la FPF";
                        $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                        $this->createAndSendInvoice($datai);
                    }

                    if ($personne->action_paiement == 'ADD_NEW_ABO') {
                        // on crée la carte
                        list($code, $reglement) = $this->saveNewAbo($personne, 'Monext');
                        $this->saveReglementEvents($reglement->id);

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
                        $this->saveSouscriptionEvents($souscription->id);
                        $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                        $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'personne_id' => $souscription->personne_id];
                        $this->createAndSendInvoice($datai);
                    } else {
                        if ($souscription->clubs_id) {
                            $this->saveSouscriptionEvents($souscription->id);
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
                    $data = ['attente_paiement' => 0, 'status' => 1, 'secure_code' => null];
                    $inscrit->update($data);
                    $formation = $inscrit->session->formation;

                    $personne = Personne::where('id', $inscrit->personne_id)->first();
//                    $personne->update(['avoir_formation' => 0]);
                    $personne->update(['creance' => 0]);

                    $email = $inscrit->personne->email;
                    $mailSent = Mail::mailer('smtp2')->to($email)->send(new ConfirmationInscriptionFormation($inscrit->session));
                    $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                    $sujet = "FPF // Inscription à la formation $formation->name";
                    $mail = new \stdClass();
                    $mail->titre = $sujet;
                    $mail->destinataire = $email;
                    $mail->contenu = $htmlContent;
                    $this->registerMail($inscrit->personne->id, $mail);

                    $sujet = "Inscription à la formation $formation->name";
                    $this->registerAction($inscrit->personne->id, 2, $sujet);

                    $description = "Inscription à la formation ".$inscrit->session->formation->name." pour la session du ".date("d/m/Y",strtotime($inscrit->session->start_date));
                    $ref = 'FORMATION-'.$inscrit->personne_id.'-'.$inscrit->session_id;
                    $datai = ['reference' => $ref, 'description' => $description, 'montant' => $inscrit->amount, 'personne_id' => $inscrit->personne->id];
                    $this->createAndSendInvoice($datai);

                    $email_dept = 'formations@federation-photo.fr';
                    Mail::mailer('smtp2')->to($email_dept)->send(new ConfirmationInscriptionDept($inscrit->session, $personne));
                    if (count($inscrit->session->formation->formateurs) > 0) {
                        foreach ($inscrit->session->formation->formateurs as $formateur) {
                            Mail::mailer('smtp2')->to($formateur->personne->email)->send(new ConfirmationInscriptionDept($inscrit->session, $personne));
                        }
                    }
                }
                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    $inscrit->delete();
                }
            }
        }



        $sessions = Session::where('attente_paiement', 1)->whereNotNull('bridge_id')->get();
        foreach ($sessions as $session) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $session->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);
            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement de la session de formation a été effectué
                    $data = ['attente_paiement' => 0, 'paiement_status' => 1];
                    $session->update($data);

                    $description = "Prise en charge de la session de formation ".$session->formation->name;
                    $contact = null;
                    if ($session->club_id) {
                        $club = Club::where('id', $session->club_id)->first();
                        $description .= " par le club ".$session->club->nom;
                        $ref = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
                        $montant = $session->reste_a_charge - $session->club->creance;
                        $datai = ['reference' => $ref, 'description' => $description, 'montant' => $montant, 'club_id' => $session->club_id];
//                        $datai = ['reference' => $ref, 'description' => $description, 'montant' => $session->pec, 'club_id' => $session->club_id];

                        // on récupère le contact du club
                        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                            ->where('utilisateurs.clubs_id', $session->club_id)
                            ->where('fonctionsutilisateurs.fonctions_id', 97)
                            ->first();

                        $club->update(['creance' => 0]);
                    } else {
                        $ur = Ur::where('id', $session->ur_id)->first();
                        $description .= " par l'UR ".$session->ur->nom;
                        $ref = 'SESSION-FORMATION-'.$session->ur_id.'-'.$session->id;
                        $montant = $session->reste_a_charge - $session->ur->creance;
                        $datai = ['reference' => $ref, 'description' => $description, 'montant' => $montant, 'ur_id' => $session->ur_id];
//                        $datai = ['reference' => $ref, 'description' => $description, 'montant' => $session->pec, 'ur_id' => $session->ur_id];

                        // on récupère le président UR
                        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                            ->where('utilisateurs.urs_id', $session->ur_id)
                            ->where('fonctionsutilisateurs.fonctions_id', 57)
                            ->first();

                        $ur->update(['creance' => 0]);
                    }
                    $session->update(['paid' => $montant]);
                    $this->createAndSendInvoice($datai);

                    if ($contact) {
                        $email = $contact->personne->email;
                        $mailSent = Mail::to($email)->send(new ConfirmationPriseEnChargeSession($session));
                        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                        $sujet = "Prise en charge de la session de formation ".$session->formation->name;
                        $mail = new \stdClass();
                        $mail->titre = $sujet;
                        $mail->destinataire = $email;
                        $mail->contenu = $htmlContent;
                        $this->registerMail($contact->personne->id, $mail);

                        $this->registerAction($contact->personne->id, 2, $sujet);
                    }
                }
                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    $data = ['attente_paiement' => 0, 'bridge_id' => null, 'bridge_link' => null];
                    $session->update($data);
                }
            }
        }
    }
}
