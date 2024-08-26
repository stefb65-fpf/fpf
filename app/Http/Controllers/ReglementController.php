<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Models\Inscrit;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Session;
use App\Models\Souscription;
use Illuminate\Http\Request;

class ReglementController extends Controller
{
    use Api;
    use Tools;
    use Invoice;
    // notification de paiement sur renouvellement - méthode appelée par Monext si l'utilisateur a payé mais n'est pas revenu sur le site
    public function notificationPaiement(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on traite le règlement
            $reglement = Reglement::where('monext_token', $request->token)->where('statut', 0)->first();
            if ($reglement) {
                // on fait le traitement
                if ($this->saveReglement($reglement)) {
                    $data =array('statut' => 1, 'numerocheque' => 'Monext '.$reglement->monext_token, 'dateenregistrement' => date('Y-m-d H:i:s'),
                        'monext_token' => null, 'monext_link' => null);
                    $reglement->update($data);

                    $this->saveInvoiceForReglement($reglement);
                }
            }
        }
        // sinon on ne fait rien
        echo 'ok';
    }

    // notification de paiement sur première adhésion individuelle - méthode appelée par Monext si l'utilisateur a payé mais n'est pas revenu sur le site
    public function notificationPaiementPersonne(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on traite le règlement
            $personne = Personne::where('monext_token', $request->token)->first();
            if ($personne) {
                if ($personne->action_paiement == 'ADD_INDIVIDUEL') {
                    $description = "Adhésion individuelle à la FPF";
                } else {
                    $description = "Abonnement à la revue France Photo";
                }
                list($code, $reglement) = $this->saveNewPersonne($personne, 'Monext');

                $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                $this->createAndSendInvoice($datai);
            }
        }
        // sinon on ne fait rien
        echo 'ok';
    }

    public function notificationPaiementNewCard(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on traite le règlement
            $personne = Personne::where('monext_token', $request->token)->first();
            if ($personne) {
                list($code, $reglement) = $this->saveNewCard($personne, 'Monext');

                $description = "Adhésion individuelle à la FPF";
                $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                $this->createAndSendInvoice($datai);

            }
        }
        // sinon on ne fait rien
        echo 'ok';
    }

    public function notificationPaiementNewAbo(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on traite le règlement
            $personne = Personne::where('monext_token', $request->token)->first();
            if ($personne) {
                list($code, $reglement) = $this->saveNewAbo($personne, 'Monext');

                $description = "Abonnement individuel à la revue France Photo";
                $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                $this->createAndSendInvoice($datai);

            }
        }
        // sinon on ne fait rien
        echo 'ok';
    }


    // notification de paiement sur sosucriptions Folrilège - méthode appelée par Monext si l'utilisateur a payé mais n'est pas revenu sur le site
    public function notificationPaiementFlorilege(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            $souscription = Souscription::where('monext_token', $request->token)->first();
            if ($souscription) {
                // on enregistre la validation de la souscription
                $data = ['statut' => 1, 'monext_token' => null, 'monext_link' => null, 'ref_reglement' => 'Monext '.$souscription->monext_token];
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
        }
        // sinon on ne fait rien
        echo 'ok';
    }

    public function notificationPaiementFormation(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on traite le règlement
            $inscrit = Inscrit::where('monext_token', $request->token)->where('attente_paiement', 1)->first();
            if ($inscrit) {
                // on met à jour le flag attente_paiement à 0 pour l'inscrit
                $data = ['attente_paiement' => 0, 'status' => 1, 'secure_code' => null];
                $inscrit->update($data);

                $personne = Personne::where('id', $inscrit->personne_id)->first();
                $personne->update(['avoir_formation' => 0]);

                $description = "Inscription à la formation ".$inscrit->session->formation->name;
                $ref = 'FORMATION-'.$inscrit->personne_id.'-'.$inscrit->session_id;
                $datai = ['reference' => $ref, 'description' => $description, 'montant' => $inscrit->amount, 'personne_id' => $inscrit->personne->id];
                $this->createAndSendInvoice($datai);
            }
        }
        // sinon on ne fait rien
        echo 'ok';
    }



    public function notificationPaiementSession(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on traite le règlement
            $session = Session::where('monext_token', $request->token)->where('attente_paiement', 1)->first();
            if ($session) {
                // on met à jour le flag attente_paiement à 0 pour l'inscrit
                $data = ['attente_paiement' => 0, 'paiement_status' => 1];
                $session->update($data);

                $description = "Prise en charge de la session de formation ".$session->formation->name;
                if ($session->club_id) {
                    $ref = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
                    $datai = ['reference' => $ref, 'description' => $description, 'montant' => $session->pec, 'club_id' => $session->club_id];
                } else {
                    $ref = 'SESSION-FORMATION-'.$session->ur_id.'-'.$session->id;
                    $datai = ['reference' => $ref, 'description' => $description, 'montant' => $session->pec, 'ur_id' => $session->ur_id];
                }

                $this->createAndSendInvoice($datai);
            }
        }
        // sinon on ne fait rien
        echo 'ok';
    }
}
