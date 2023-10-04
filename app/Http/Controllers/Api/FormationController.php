<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmationInscriptionFormationAttente;
use App\Mail\ConfirmVote;
use App\Mail\RelanceReglement;
use App\Mail\SendAlertSupport;
use App\Mail\SendCodeForVote;
use App\Models\Candidat;
use App\Models\Election;
use App\Models\Formateur;
use App\Models\Inscrit;
use App\Models\Motion;
use App\Models\Pays;
use App\Models\Session;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class FormationController extends Controller
{
    use Api;
    use Tools;

    protected function getFormateur(Request $request)
    {
        $formateur = Formateur::where('id', $request->id)->first();
        $formateur->nom = $formateur->personne->nom;
        $formateur->prenom = $formateur->personne->prenom;
        if (!$formateur) {
            return new JsonResponse(['success' => 'OK'], 200);
        } else {
            return new JsonResponse(['success' => 'OK', 'personne' => $formateur], 200);
        }
    }

    protected function getReviews(Request $request)
    {
        $reviews = [];
        //TODO: création de la table reviews et des reviews associés à une formation
        //$reviews["liste"] = Reviews::where('id', $request->id)->first();

        $reviews["liste"] = [];

        if (!$reviews) {
            return new JsonResponse(['success' => 'OK'], 200);
        } else {
            return new JsonResponse(['success' => 'OK', 'reviews' => $reviews], 200);
        }
    }

    public function payByVirement(Request $request)
    {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => 'session de formation inexistante'], 400);
        }
        if (sizeof($session->inscrits->where('status', 1)->where('attente', 0)) >= $session->places) {
            return new JsonResponse(['erreur' => 'session de formation complète'], 400);
        }
        // on regarde si le user n'est pas déjà inscrits
        foreach ($session->inscrits as $inscrit) {
            if ($inscrit->personne_id == $user->id) {
                return new JsonResponse(['erreur' => 'utilisateur déjà inscrit'], 400);
            }
        }
        // on inscrit le user
        $datai = ['session_id' => $session->id, 'personne_id' => $user->id, 'attente_paiement' => 1];
        $inscrit = Inscrit::create($datai);

        $url = 'https://api.bridgeapi.io/v2/payment-links';
        $transaction = new \stdClass();
        $transaction->amount = floatval($session->price);
        $transaction->currency = 'EUR';
        $ref = 'FORMATION-'.$inscrit->personne_id.'-'.$inscrit->session_id;
        $transaction->label = $ref;
        $expired_date = new \DateTime(date('Y-m-d H:i:s'));
        $expired_date->add(new \DateInterval('P1D'));

        $bridge_datas = [
            "user" => [
                "first_name" => $inscrit->personne->prenom,
                "last_name" => $inscrit->personne->nom
            ],
            "expired_date" => $expired_date->format('c'),
            "client_reference" => $ref,
            "transactions" => [
                $transaction
            ],
            "callback_url" => env('APP_URL') . "formations/attente_paiement_validation/".$session->formation->id,
        ];

        list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
        if ($status == 200) {
            $reponse = json_decode($reponse);
            $inscrit->update(['bridge_id' => $reponse->id, 'bridge_link' => $reponse->url]);
            return new JsonResponse(['url' => $reponse->url], 200);
        } else {
            $inscrit->delete();
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }

    public function payByCb(Request $request)
    {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => 'session de formation inexistante'], 400);
        }
        if (sizeof($session->inscrits->where('status', 1)->where('attente', 0)) >= $session->places) {
            return new JsonResponse(['erreur' => 'session de formation complète'], 400);
        }
        // on regarde si le user n'est pas déjà inscrits
        foreach ($session->inscrits as $inscrit) {
            if ($inscrit->personne_id == $user->id) {
                return new JsonResponse(['erreur' => 'utilisateur déjà inscrit'], 400);
            }
        }
        // on inscrit le user
        $datai = ['session_id' => $session->id, 'personne_id' => $user->id, 'attente_paiement' => 1];
        $inscrit = Inscrit::create($datai);

        $montant = intval($session->price * 100);
        $urls = [
            'cancelURL' => env('APP_URL') . "formations/cancel_paiement",
            'returnURL' => env('APP_URL') . "formations/validation_paiement",
            'notificationURL' => env('APP_URL') . "formations/notification_paiement",
        ];
        $personne = [
            'email' => $inscrit->personne->email,
            'prenom' => $inscrit->personne->prenom,
            'nom' => $inscrit->personne->nom,
        ];
        $ref = 'FORMATION-'.$inscrit->personne_id.'-'.$inscrit->session_id;
        $result = $this->callMonext($montant, $urls, $ref, $personne);
        if ($result['code'] == '00000') {
            $inscrit->update(['monext_token' => $result['token'], 'monext_link' => $result['redirectURL']]);
            return new JsonResponse(['url' => $result['redirectURL']], 200);
        } else {
            $inscrit->delete();
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }

    public function inscriptionAttente(Request $request) {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => 'session de formation inexistante'], 400);
        }
        if (sizeof($session->inscrits->where('status', 1)->where('attente', 1)) >= $session->waiting_places) {
            return new JsonResponse(['erreur' => 'session de formation complète'], 400);
        }
        // on regarde si le user n'est pas déjà inscrits
        foreach ($session->inscrits as $inscrit) {
            if ($inscrit->personne_id == $user->id) {
                return new JsonResponse(['erreur' => 'utilisateur déjà inscrit'], 400);
            }
        }
        // on enregistre l'inscription en attente
        $datai = ['session_id' => $session->id, 'personne_id' => $user->id, 'attente' => 1, 'status' => 1];
        Inscrit::create($datai);

        // on envoie le mail pour confirmer l'inscription en attente
        $email = $user->email;
        $mailSent = Mail::to($email)->send(new ConfirmationInscriptionFormationAttente($session));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $sujet = substr("FPF // Inscription en liste d'attente à la formation ".$session->formation->name, 0, 255);
        $mail = new \stdClass();
        $mail->titre = $sujet;
        $mail->destinataire = $email;
        $mail->contenu = $htmlContent;
        $this->registerMail($user->id, $mail);

        $sujet = substr("Inscription en liste d'attente à la formation ".$session->formation->name, 0, 255);
        $this->registerAction($user->id, 2, $sujet);

        return new JsonResponse(['success' => 'OK'], 200);
    }
}
