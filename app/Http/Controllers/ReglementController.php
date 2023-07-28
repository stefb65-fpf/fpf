<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\Tools;
use App\Models\Reglement;
use Illuminate\Http\Request;

class ReglementController extends Controller
{
    use Api;
    use Tools;
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
            // TODO : on traite le règlement pour la personne
        }
        // sinon on ne fait rien
        echo 'ok';
    }
}
