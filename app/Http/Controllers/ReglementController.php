<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Models\Personne;
use App\Models\Reglement;
use Illuminate\Http\Request;

class ReglementController extends Controller
{
    use Api;
    use Tools;
    use Invoice;
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
                    $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'club_id' => $souscription->personne_id];
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
}
