<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Souscription;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlorilegeController extends Controller
{
    use Api;
    public function orderFlorilege(Request $request) {
        $personne = Personne::where('id', $request->personne_id)->first();
        if (!$personne) {
            return new JsonResponse(['error' => "La personne n'existe pas"], 400);
        }
//        $config = Configsaison::where('id', 1)->selectRaw('prixflorilegefrance, prixflorilegeetranger, datedebutflorilege, datefinflorilege')->first();
        $config = Configsaison::where('id', 1)->selectRaw('datedebutflorilege, datefinflorilege')->first();
        $tarif_florilege_france = Tarif::where('statut', 0)->where('id', 21)->first();
        $tarif_florilege_etranger = Tarif::where('statut', 0)->where('id', 22)->first();
        $config->prixflorilegefrance = $tarif_florilege_france->tarif;
        $config->prixflorilegeetranger = $tarif_florilege_etranger->tarif;
        if (!(date('Y-m-d') >= $config->datedebutflorilege && date('Y-m-d') <= $config->datefinflorilege)) {
            return new JsonResponse(['error' => "La commande n'est pas permise"], 400);
        }
        // on regarde si le montant total est bien égal au montant attendu
        $montant_attendu = $request->nb * $config->prixflorilegefrance;
        if ($montant_attendu != $request->montant) {
            return new JsonResponse(['error' => "Le montant total n'est pas cohérent"], 400);
        }
        $montant_cents = $montant_attendu * 100;
        $ref = 'FLORILEGE-'.$personne->id.'-'.date('y');
        $last_souscription = Souscription::where('reference', 'LIKE', $ref.'%')->orderBy('id', 'DESC')->first();
        $num = $last_souscription ? intval(substr($last_souscription->reference, -4)) + 1 : 1;
        $ref .= '-'.str_pad($num, 4, '0', STR_PAD_LEFT);

        if ($request->type == 'bridge') {
            $url = 'https://api.bridgeapi.io/v2/payment-links';
            $transaction = new \stdClass();
            $transaction->amount = $montant_attendu;
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
                "client_reference" => strval($personne->id),
                "transactions" => [
                    $transaction
                ],
                "callback_url" => env('APP_URL') . "utilisateurs/attente_paiement_validation",
            ];


            list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
            if ($status == 200) {
                $reponse = json_decode($reponse);
                $datas = [
                    'personne_id' => $personne->id,
                    'reference' => $ref,
                    'nbexemplaires' => $request->nb,
                    'montanttotal' => $montant_attendu,
                    'bridge_id' => $reponse->id,
                    'bridge_link' => $reponse->url,
                    'utilisateur_id' => $request->utilisateur_id
                ];
                $souscription = Souscription::create($datas);
                if ($souscription) {
                    return new JsonResponse(['url' => $reponse->url], 200);
                } else {
                    return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
                }
            } else {
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        } else {
            $urls = [
                'cancelURL' => env('APP_URL') . "cancel_paiement_florilege",
                'returnURL' => env('APP_URL') . "validation_paiement_carte_florilege",
                'notificationURL' => env('APP_URL') . "florilege/notification_paiement",
            ];
            $user = [
                'email' => $personne->email,
                'prenom' => $personne->prenom,
                'nom' => $personne->nom,
            ];
            $result = $this->callMonext($montant_cents, $urls, $ref, $user);
            if ($result['code'] == '00000') {
                $datas = ['personne_id' => $personne->id, 'reference' => $ref, 'nbexemplaires' => $request->nb, 'montanttotal' => $request->montant,
                    'monext_token' => $result['token'], 'monext_link' => $result['redirectURL'], 'utilisateur_id' => $request->utilisateur_id];
                $souscription = Souscription::create($datas);
                if ($souscription) {
                    return new JsonResponse(['url' => $result['redirectURL']], 200);
                } else {
                    return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
                }
            } else {
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        }
    }

    public function orderFlorilegeClub(Request $request) {
        $club = Club::where('id', $request->club_id)->first();
        if (!$club) {
            return new JsonResponse(['error' => "Le club n'existe pas"], 400);
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->first();

//        $config = Configsaison::where('id', 1)->selectRaw('prixflorilegefrance, prixflorilegeetranger, datedebutflorilege, datefinflorilege')->first();
        $config = Configsaison::where('id', 1)->selectRaw('datedebutflorilege, datefinflorilege')->first();
        $tarif_florilege_france = Tarif::where('statut', 0)->where('id', 21)->first();
        $tarif_florilege_etranger = Tarif::where('statut', 0)->where('id', 22)->first();
        $config->prixflorilegefrance = $tarif_florilege_france->tarif;
        $config->prixflorilegeetranger = $tarif_florilege_etranger->tarif;
        if (!(date('Y-m-d') >= $config->datedebutflorilege && date('Y-m-d') <= $config->datefinflorilege)) {
            return new JsonResponse(['error' => "La commande n'est pas permise"], 400);
        }
        // on regarde si le montant total est bien égal au montant attendu
        $montant_attendu = $request->nb * $config->prixflorilegefrance;
        if ($montant_attendu != $request->montant) {
            return new JsonResponse(['error' => "Le montant total n'est pas cohérent"], 400);
        }
        $montant_cents = $montant_attendu * 100;
        $ref = 'FLORILEGE-'.str_pad($club->numero, 4, '0', STR_PAD_LEFT).'-'.date('y');
        $last_souscription = Souscription::where('reference', 'LIKE', $ref.'%')->orderBy('id', 'DESC')->first();
        $num = $last_souscription ? intval(substr($last_souscription->reference, -4)) + 1 : 1;
        $ref .= '-'.str_pad($num, 4, '0', STR_PAD_LEFT);

        if ($request->type == 'bridge') {
            $url = 'https://api.bridgeapi.io/v2/payment-links';
            $transaction = new \stdClass();
            $transaction->amount = $montant_attendu;
            $transaction->currency = 'EUR';
            $transaction->label = $ref;

            $expired_date = new \DateTime(date('Y-m-d H:i:s'));
            $expired_date->add(new \DateInterval('P1D'));

            $bridge_datas = [
                "user" => [
                    "first_name" => $contact->personne->prenom,
                    "last_name" => $contact->personne->nom
                ],
                "expired_date" => $expired_date->format('c'),
                "client_reference" => strval($club->id),
                "transactions" => [
                    $transaction
                ],
                "callback_url" => env('APP_URL') . "clubs/florilege/attente_paiement_validation",
            ];


            list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
            if ($status == 200) {
                $reponse = json_decode($reponse);
                $datas = ['clubs_id' => $club->id, 'reference' => $ref, 'nbexemplaires' => $request->nb, 'montanttotal' => $montant_attendu,  'bridge_id' => $reponse->id, 'bridge_link' => $reponse->url];
                $souscription = Souscription::create($datas);
                if ($souscription) {
                    return new JsonResponse(['url' => $reponse->url], 200);
                } else {
                    return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
                }
            } else {
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        } else {
            $urls = [
                'cancelURL' => env('APP_URL') . "cancel_paiement_florilege_club",
                'returnURL' => env('APP_URL') . "validation_paiement_carte_florilege_club",
                'notificationURL' => env('APP_URL') . "florilege/notification_paiement",
            ];
            $user = [
                'email' => $contact->personne->email,
                'prenom' => $contact->personne->prenom,
                'nom' => $contact->personne->nom,
            ];
            $result = $this->callMonext($montant_cents, $urls, $ref, $user);
            if ($result['code'] == '00000') {
                $datas = ['clubs_id' => $club->id, 'reference' => $ref, 'nbexemplaires' => $request->nb, 'montanttotal' => $request->montant,
                    'monext_token' => $result['token'], 'monext_link' => $result['redirectURL']];
                $souscription = Souscription::create($datas);
                if ($souscription) {
                    return new JsonResponse(['url' => $result['redirectURL']], 200);
                } else {
                    return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
                }
            } else {
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        }
    }
}
