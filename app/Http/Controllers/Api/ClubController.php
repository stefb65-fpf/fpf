<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    use Api;
    use Tools;

    public function clubActivite(Request $request, Club $club)
    {
        $club = Club::where('id', $request->club)->first();
        $club_activites = DB::table('activitesclubs')->where('clubs_id', $request->club)->get();
        $activites = [];
        $isInArray = false;
        foreach ($club_activites as $activite) {
            $activites[] = $activite->activites_id;
            if ($activite->activites_id == $request->clubPreferences) {
                $isInArray = true;
            }
        }
        if ($isInArray) {
            //on enleve la ligne correspondant de la table pivot
            DB::table('activitesclubs')->where('clubs_id', $request->club)->where('activites_id',$request->clubPreferences)->delete();
        } else {
            //on ajoute la ligne correspondant à la table pivot
            $data_ap = array('activites_id' => $request->clubPreferences, 'clubs_id' => $request->club);
            DB::table('activitesclubs')->insert($data_ap);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification des activités du club \"".$club->nom."\"");
        }
        return new JsonResponse(true, 200);
    }
    public function clubEquipement(Request $request, Club $club)
    {
        $club = Club::where('id', $request->club)->first();
        $club_equipements = DB::table('equipementsclubs')->where('clubs_id', $request->club)->get();
        $equipements = [];
        $isInArray = false;
        foreach ($club_equipements  as $equipement) {
            $equipements[] = $equipement->equipements_id;
            if ($equipement->equipements_id == $request->clubPreferences) {
                $isInArray = true;
            }
        }
        if ($isInArray) {
            //on enleve la ligne correspondant de la table pivot
            DB::table('equipementsclubs')->where('clubs_id', $request->club)->where('equipements_id',$request->clubPreferences)->delete();
        } else {
            //on ajoute la ligne correspondant à la table pivot
            $data_ap = array('equipements_id' => $request->clubPreferences, 'clubs_id' => $request->club);
            DB::table('equipementsclubs')->insert($data_ap);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification des équipements du club \"".$club->nom."\"");
        }
        return new JsonResponse(true, 200);
    }

    public function payByVirement(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement non trouvé'], 400);
        }
        $club = Club::where('id', $reglement->clubs_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'club non trouvé'], 400);
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->where('utilisateurs.clubs_id', $club->id)
            ->first();
        if (!$contact) {
            return new JsonResponse(['erreur' => 'contact non trouvé'], 400);
        }

        $url = 'https://api.bridgeapi.io/v2/payment-links';
        $transaction = new \stdClass();
        $transaction->amount = floatval($reglement->montant);
        $transaction->currency = 'EUR';
        $transaction->label = 'FPF - '.$reglement->reference;

        $expired_date = new \DateTime(date('Y-m-d H:i:s'));
        $expired_date->add(new \DateInterval('P1D'));

        $bridge_datas = [
            "user" => [
                "first_name" => $contact->personne->prenom,
                "last_name" => $contact->personne->nom
            ],
            "expired_date" => $expired_date->format('c'),
            "client_reference" => $reglement->reference,
            "transactions" => [
                $transaction
            ],
            "callback_url" => env('APP_URL') . "clubs/attente_paiement_validation",
        ];

        list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
        if ($status == 200) {
            $reponse = json_decode($reponse);
            $reglement->update(['bridge_id' => $reponse->id, 'bridge_link' => $reponse->url]);
            return new JsonResponse(['url' => $reponse->url], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }

    public function payByCb(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement non trouvé'], 400);
        }
        $club = Club::where('id', $reglement->clubs_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'club non trouvé'], 400);
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->where('utilisateurs.clubs_id', $club->id)
            ->first();
        if (!$contact) {
            return new JsonResponse(['erreur' => 'contact non trouvé'], 400);
        }
        $montant = intval($reglement->montant * 100);
        $urls = [
            'cancelURL' => env('APP_URL') . "clubs/reglements",
            'returnURL' => env('APP_URL') . "clubs/validation_paiement_carte",
            'notificationURL' => env('APP_URL') . "reglements/notification_paiement",
        ];
        $user = [
            'email' => $contact->personne->email,
            'prenom' => $contact->personne->prenom,
            'nom' => $contact->personne->nom,
        ];
        $result = $this->callMonext($montant, $urls, $reglement->reference, $user);
        if ($result['code'] == '00000') {
            $reglement->update(['monext_token' => $result['token'], 'monext_link' => $result['redirectURL']]);
            return new JsonResponse(['url' => $result['redirectURL']], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }
}
