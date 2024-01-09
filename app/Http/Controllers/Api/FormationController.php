<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Mail\AnswerSupport;
use App\Mail\AskFormation;
use App\Mail\ConfirmationInscriptionFormation;
use App\Mail\ConfirmationInscriptionFormationAttente;
use App\Mail\ConfirmVote;
use App\Mail\RelanceReglement;
use App\Mail\SendAlertSupport;
use App\Mail\SendCodeForVote;
use App\Models\Candidat;
use App\Models\Demande;
use App\Models\Election;
use App\Models\Evaluation;
use App\Models\Evaluationsitem;
use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Inscrit;
use App\Models\Interest;
use App\Models\Motion;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Session;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        $formation = Formation::where('id', $request->id)->first();
        if (!$formation) {
            return new JsonResponse(['success' => 'KO'], 400);
        }

        $tab_reviews = [];
        foreach ($formation->sessions as $session) {
            // on récupère toutes les évaluation avec comment non nul pour la session
            $reviews = $session->evaluations->where('comment', '!=', '');
            foreach ($reviews as $review) {
                $tab_reviews[$review->created_at->format('YmdHis')] = [
                    'comment' => $review->comment,
                    'nom' => $review->personne->nom,
                    'prenom' => $review->personne->prenom,
                    'date' => $review->created_at->format('d F Y'),
                ];
                // on récupère toutes les évaluations pour la session et la personne donnée
                $evaluations = Evaluation::where('session_id', $session->id)->where('personne_id', $review->personne_id)->where('stars', '!=', 0)->get();
                $total = 0; $nb = 0;
                foreach ($evaluations as $evaluation) {
                    $total += $evaluation->stars;
                    $nb++;
                }
                $tab_reviews[$review->created_at->format('YmdHis')]['note'] = round($total / $nb, 2);
            }
        }
        krsort($tab_reviews);
        return new JsonResponse(['success' => 'OK', 'reviews' => $tab_reviews, 'nb' => sizeof($tab_reviews)], 200);
    }

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
        $price = $session->price_not_member;
        if ($user->cartes) {
            foreach ($user->cartes as $carte) {
                if (in_array($carte->statut, [2, 3])) {
                    $price = $session->price;
                }
            }
        }
        if ($personne->avoir_formation > 0) {
            $price -= $personne->avoir_formation;
            if ($price < 0) $price = 0;
        }
        if ($request->link == '') {
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
            $datai = ['session_id' => $session->id, 'personne_id' => $user->id, 'attente_paiement' => 1, 'amount' => $price];
            $inscrit = Inscrit::create($datai);
        } else {
            $inscrit = Inscrit::where('secure_code', $request->link)->first();
            if (!$inscrit) {
                return new JsonResponse(['erreur' => 'inscrit inexistant'], 400);
            }
        }

        $url = 'https://api.bridgeapi.io/v2/payment-links';
        $transaction = new \stdClass();
        $transaction->amount = floatval($price);
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
            if ($request->link == '') {
                $inscrit->delete();
            }
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
        $price = $session->price_not_member;
        if ($user->cartes) {
            foreach ($user->cartes as $carte) {
                if (in_array($carte->statut, [2, 3])) {
                    $price = $session->price;
                }
            }
        }
        if ($personne->avoir_formation > 0) {
            $price -= $personne->avoir_formation;
            if ($price < 0) $price = 0;
        }
        if ($request->link == '') {
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
            $datai = ['session_id' => $session->id, 'personne_id' => $user->id, 'attente_paiement' => 1, 'amount' => $price];
            $inscrit = Inscrit::create($datai);
        } else {
            $inscrit = Inscrit::where('secure_code', $request->link)->first();
            if (!$inscrit) {
                return new JsonResponse(['erreur' => 'inscrit inexistant'], 400);
            }
        }

        $montant = intval($price * 100);
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
            if ($request->link == '') {
                $inscrit->delete();
            }
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }

    public function saveWithoutPaiement(Request $request) {
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
        $datai = ['session_id' => $session->id, 'personne_id' => $user->id, 'status' => 1];
        Inscrit::create($datai);

        // on débite l'avoir du compte du user
        $personne->update(['avoir_formation' => $personne->avoir_formation - $session->price]);

        // on evnoei le mail de confirmation d'inscription
        $email = $user->email;
        $mailSent = Mail::to($email)->send(new ConfirmationInscriptionFormation($session));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $sujet = substr("FPF // Inscription à la formation ".$session->formation->name, 0, 255);
        $mail = new \stdClass();
        $mail->titre = $sujet;
        $mail->destinataire = $email;
        $mail->contenu = $htmlContent;
        $this->registerMail($user->id, $mail);

        $sujet = substr("Inscription à la formation ".$session->formation->name, 0, 255);
        $this->registerAction($user->id, 2, $sujet);

        return new JsonResponse(['success' => 'OK'], 200);
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

    public function addInscritToSession(Request $request) {
        $personne = Personne::where('email', $request->email)->first();
        if (!$personne) {
            return new JsonResponse(['erreur' => "L'adresse email ne correspond à aucune personne"], 400);
        }
        $session = Session::where('id', $request->session_id)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => "La session de formation n'existe pas"], 400);
        }
        // on regarde si la personn n'est pas déjà inscrite
        $inscrit = Inscrit::where('personne_id', $personne->id)->where('session_id', $session->id)->first();
        if ($inscrit) {
            return new JsonResponse(['erreur' => "La personne est déjà inscrite à cette session"], 400);
        }

        // on ajoute l'inscrit
        $datai = ['session_id' => $session->id, 'personne_id' => $personne->id, 'attente' => 0, 'status' => 1];
        Inscrit::create($datai);
        return new JsonResponse([], 200);
    }

    public function setInterest(Request $request) {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $formation = Formation::where('id', $request->formation)->first();
        if (!$formation) {
            return new JsonResponse(['erreur' => "Formation introuvable"], 400);
        }
        $interest = Interest::where('formation_id', $formation->id)->where('personne_id', $user->id)->first();
        if (!$interest) {
            // on ajoute l'interet
            $datai = ['formation_id' => $formation->id, 'personne_id' => $user->id];
            Interest::create($datai);
        } else {
            // on supprime l'intérêt
            $interest->delete();
        }
        return new JsonResponse([], 200);
    }

    public function askFormation(Request $request) {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $formation = Formation::where('id', $request->formation)->first();
        if (!$formation) {
            return new JsonResponse(['erreur' => "Formation introuvable"], 400);
        }
        // on regarde si une demande n'a pas déjà été faite
        $query = Demande::where('formation_id', $formation->id);
        if ($request->level == 'club') {
            $query->where('club_id', $user->cartes[0]->clubs_id);
        } else {
            $query->where('ur_id', $user->cartes[0]->urs_id);
        }
        $demande = $query->first();
        if ($demande) {
            return new JsonResponse(['erreur' => "Une demande a déjà été faite pour cette formation"], 400);
        }
        // on ajoute la demande
        $datai = ['formation_id' => $formation->id];
        if ($request->level == 'club') {
            $datai['club_id'] = $user->cartes[0]->clubs_id;
        } else {
            $datai['ur_id'] = $user->cartes[0]->urs_id;
        }
        $demande = Demande::create($datai);
        $mail_formation = 'dpt.formation@federation-photo.fr';
        if ($request->level == 'club') {
            $str = 'club '.$demande->club->nom;
        } else {
            $str = 'UR '.$demande->ur->nom;
        }
        Mail::to($mail_formation)->send(new AskFormation($formation, $str));
        return new JsonResponse([], 200);
    }

    public function generatePdfEvaluations(Request $request) {
        $formation = Formation::where('id', $request->formation)->first();
        if (!$formation) {
            return new JsonResponse(['erreur' => "Formation introuvable"], 400);
        }
        $tab_evaluations = array();
        $tab_reviews = array();
        foreach ($formation->sessions as $session) {
            foreach ($session->evaluations as $evaluation) {
                if ($evaluation->comment != '') {
                    $tab_reviews[] = $evaluation->comment;
                } else {
                    if (!isset($tab_evaluations[$evaluation->evaluationsitem_id])) {
                        // on cherche l'item
                        $item = Evaluationsitem::where('id', $evaluation->evaluationsitem_id)->first();
                        if ($item) {
                            $tab_evaluations[$evaluation->evaluationsitem_id]['name'] = $item->evaluationstheme->name."\n".$item->name;
                        }
                        $tab_evaluations[$evaluation->evaluationsitem_id]['note'] = $evaluation->stars;
                        $tab_evaluations[$evaluation->evaluationsitem_id]['nb'] = 1;
                    } else {
                        $total = $tab_evaluations[$evaluation->evaluationsitem_id]['note'] * $tab_evaluations[$evaluation->evaluationsitem_id]['nb'];
                        $total += $evaluation->stars;
                        $tab_evaluations[$evaluation->evaluationsitem_id]['note'] = round($total / ($tab_evaluations[$evaluation->evaluationsitem_id]['nb'] + 1), 1);
                        $tab_evaluations[$evaluation->evaluationsitem_id]['nb'] = $tab_evaluations[$evaluation->evaluationsitem_id]['nb'] + 1;
                    }
                }
            }
        }

        $dir = storage_path() . '/app/public/uploads/evaluations/' . date('Y');
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $name = 'evaluations-' . $formation->id . '-'.date('YmdHis').'.pdf';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.evaluations', compact('tab_evaluations', 'tab_reviews', 'formation'))
            ->setWarnings(false)
            ->setPaper('a4', 'portrait')
            ->save($dir . '/' . $name);
        return new JsonResponse(['file' => $name, 'year' => date('Y')], 200);
    }

    public function cancelInscription(Request $request) {
        // on regarde si la session existe
        $session = Session::where('id', $request->ref)->first();
        if (!$session) {
            return new JsonResponse(['erreur' => "Session de formation introuvable"], 400);
        }
        // on regarde si le user est inscrit à la session
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse(['erreur' => 'session utilisateur inexistante'], 400);
        }
        $inscrit = Inscrit::where('session_id', $session->id)->where('personne_id', $user->id)->first();
        if (!$inscrit) {
            return new JsonResponse(['erreur' => "Vous n'êtes pas inscrit à cette session"], 400);
        }
        $amount = $inscrit->amount;
        // on supprime l'inscription
        $inscrit->delete();

        // on crédite le compte du user
        $personne = Personne::where('id', $user->id)->first();
        $personne->update(['avoir_formation' => $personne->avoir_formation + $amount]);

        return new JsonResponse(['success' => "Votre désinscription a été prise en compte et votre compte formation a été crédité de $amount €"], 200);
    }
}
