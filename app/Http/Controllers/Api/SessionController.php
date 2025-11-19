<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Inscrit;
use App\Models\Personne;
use App\Models\Session;
use App\Models\Ur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    use Api;
    use Tools;

    public function payByVirement(Request $request)
    {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $personne = Personne::where('id', $user->id)->first();
        if (!$personne) {
            return new JsonResponse(['erreur' => 'utilisateur inexistant'], 400);
        }
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => 'session de formation inexistante'], 400);
        }
        if ($session->paiement_status == 1) {
            return new JsonResponse(['erreur' => 'session de formation déjà payée'], 400);
        }
        $price = $session->reste_a_charge;
//        $price = $session->pec;
        if ($price == 0) {
            return new JsonResponse(['erreur' => 'session de formation gratuite'], 400);
        }
        $creance = 0;
        if ($session->club_id) {
            $ref = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
            $creance = $session->club->creance;
        } else {
            $ref = 'SESSION-FORMATION-'.$session->ur_id.'-'.$session->id;
            $creance = $session->ur->creance;
        }
        if ($creance > $price) {
            return new JsonResponse(['erreur' => 'la créance est supérieure au coût de la formation'], 400);
        }
        $price -= $creance;

        $url = 'https://api.bridgeapi.io/v2/payment-links';
        $transaction = new \stdClass();
        $transaction->amount = floatval($price);
        $transaction->currency = 'EUR';
        $transaction->label = $ref;
        $expired_date = new \DateTime(date('Y-m-d H:i:s'));
        $expired_date->add(new \DateInterval('P1D'));

        $bridge_datas = [
            "user" => [
                "first_name" => $personne->prenom,
                "last_name" => $personne->nom
            ],
            "expired_date" => $expired_date->format('c'),
            "client_reference" => $ref,
            "transactions" => [
                $transaction
            ],
            "callback_url" => env('APP_URL') . "sessions/attente_paiement_validation/".$session->id,
        ];

        list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
        if ($status == 200) {
            $reponse = json_decode($reponse);
            $session->update(['bridge_id' => $reponse->id, 'bridge_link' => $reponse->url, 'attente_paiement' => 1]);
            return new JsonResponse(['url' => $reponse->url], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }

    public function payByCb(Request $request)
    {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $personne = Personne::where('id', $user->id)->first();
        if (!$personne) {
            return new JsonResponse(['erreur' => 'utilisateur inexistant'], 400);
        }
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => 'session de formation inexistante'], 400);
        }
        if ($session->paiement_status == 1) {
            return new JsonResponse(['erreur' => 'session de formation déjà payée'], 400);
        }
        $price = $session->reste_a_charge;
        if ($price == 0) {
            return new JsonResponse(['erreur' => 'session de formation gratuite'], 400);
        }
        $creance = 0;
        if ($session->club_id) {
            $ref = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
            $creance = $session->club->creance;
        } else {
            $ref = 'SESSION-FORMATION-'.$session->ur_id.'-'.$session->id;
            $creance = $session->ur->creance;
        }
        if ($creance > $price) {
            return new JsonResponse(['erreur' => 'la créance est supérieure au coût de la formation'], 400);
        }
        $price -= $creance;

//        $price = $session->pec;


        $montant = intval($price * 100);
        $urls = [
            'cancelURL' => env('APP_URL') . "sessions/cancel_paiement",
            'returnURL' => env('APP_URL') . "sessions/validation_paiement",
            'notificationURL' => env('APP_URL') . "sessions/notification_paiement",
        ];
        $personne = [
            'email' => $personne->email,
            'prenom' => $personne->prenom,
            'nom' => $personne->nom,
        ];

        $result = $this->callMonext($montant, $urls, $ref, $personne);
        if ($result['code'] == '00000') {
            $session->update(['monext_token' => $result['token'], 'monext_link' => $result['redirectURL'], 'attente_paiement' => 1]);
            return new JsonResponse(['url' => $result['redirectURL']], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }


    public function payByCreance(Request $request)
    {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $personne = Personne::where('id', $user->id)->first();
        if (!$personne) {
            return new JsonResponse(['erreur' => 'utilisateur inexistant'], 400);
        }
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => 'session de formation inexistante'], 400);
        }
        if ($session->paiement_status == 1) {
            return new JsonResponse(['erreur' => 'session de formation déjà payée'], 400);
        }
        $price = $session->reste_a_charge;
        if ($price == 0) {
            return new JsonResponse(['erreur' => 'session de formation gratuite'], 400);
        }
        if ($session->club_id) {
            $creance = $session->club->creance;
        } else {
            $creance = $session->ur->creance;
        }
        if ($creance < $price) {
            return new JsonResponse(['erreur' => 'la créance est inférieure au coût de la formation'], 400);
        }
        $new_creance = $creance - $price;

        $data = ['attente_paiement' => 0, 'paiement_status' => 1];
        $session->update($data);

        $description = "Prise en charge de la session de formation ".$session->formation->name;
        if ($session->club_id) {
            $club = Club::where('id', $session->club_id)->first();
            $description .= " par le club ".$session->club->nom;
            $ref = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
            $datai = ['reference' => $ref, 'description' => $description, 'montant' => 0, 'club_id' => $session->club_id];

            $club->update(['creance' => $new_creance]);
        } else {
            $ur = Ur::where('id', $session->ur_id)->first();
            $description .= " par l'UR ".$session->ur->nom;
            $ref = 'SESSION-FORMATION-'.$session->ur_id.'-'.$session->id;
            $datai = ['reference' => $ref, 'description' => $description, 'montant' => 0, 'ur_id' => $session->ur_id];

            $ur->update(['creance' => $new_creance]);
        }
        $session->update(['paid' => 0]);

        $this->createAndSendInvoice($datai);

        return new JsonResponse(['success' => true], 200);

    }
}
