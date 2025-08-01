<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\FormationTools;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Mail\ConfirmationInscriptionFormation;
use App\Mail\ConfirmationPriseEnChargeSession;
use App\Mail\SendInvoice;
use App\Models\Club;
use App\Models\Evaluation;
use App\Models\Evaluationstheme;
use App\Models\Formation;
use App\Models\Inscrit;
use App\Models\Interest;
use App\Models\Personne;
use App\Models\Session;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use function Psy\debug;

class FormationController extends Controller
{
    use FormationTools;
    use Api;
    use Invoice;
    use Tools;

    public function __construct()
    {
        $this->middleware('checkLogin')->except(['listePublique', 'detailPublique']);
    }

    public function accueil()
    {
        $formations = Formation::where('published', 1)->orderByDesc('created_at')->get();
        foreach ($formations as $formation) {
            if (sizeof($formation->sessions->where('start_date', '>=', date('Y-m-d'))) > 0) {
                $formation->first_date = $formation->sessions->sortBy('start_date')->where('start_date', '>=', date('Y-m-d'))->first()->start_date;
            } else {
                $formation->first_date = '2222-01-01';
            }

            $formation->location = $this->getFormationCities($formation, $formation->location);
        }
        $formations = $formations->sortBy('first_date');

        return view('formations.accueil', compact('formations'));
    }

    public function detail(Formation $formation)
    {
        $user = session()->get('user');
        $personne = Personne::where('id', $user->id)->first();
        $price_adherent = 0;
        if ($user->cartes) {
            foreach ($user->cartes as $carte) {
                if (in_array($carte->statut, [2, 3])) {
                    $price_adherent = 1;
                }
            }
        }
        $personne->price_adherent = $price_adherent;
        $inscriptions = [];
        foreach ($personne->inscrits->where('status', 1) as $inscrit) {
            $inscriptions[] = $inscrit->session_id;
        }
        foreach ($formation->sessions as $k => $session) {
            $session_full = 0;
            if ($session->ur_id != '') {
                // on regarde si l'ur de la session est la même sque celui du user
                if ($user->cartes[0]->urs_id != $session->ur_id) {
                    $session_full = 1;
                }
            }
            if ($session->club_id != '') {
                $club = Club::where('id', $session->club_id)->selectRaw('nom')->first();
                if ($club) {
                    $formation->sessions[$k]->nom_club = $club->nom;
                }
                // on regarde si l'ur de la session est la même sque celui du user
                if ($user->cartes[0]->clubs_id != $session->club_id) {
                    $session_full = 1;
                }
            }
            $formation->sessions[$k]->full = $session_full;
        }
        $formation->location = strlen($formation->location) ? $formation->location : $this->getFormationCities($formation);
        $formation->interest = $this->getFormationInterest($formation);
        return view('formations.detail', compact('formation', 'personne', 'inscriptions'));
    }

    protected function getFormationInterest($formation) {
        $interest = Interest::where('formation_id', $formation->id)->where('personne_id', session()->get('user')->id)->first();
        if (!$interest) {
            return false;
        }
        return true;
    }

    public function cancelPaiement(Request $request)
    {
        $inscrit = Inscrit::where('monext_token', $request->token)->first();
        if ($inscrit) {
            $formation = $inscrit->session->formation;
            if ($inscrit->secure_code != '') {
                $inscrit->delete();
            }
            return redirect()->route('formations.detail', $formation->id)->with('error', "Votre paiement a été annulé");
        } else {
            return redirect()->route('formations.accueil')->with('error', "Le paiement a été annulé");
        }
    }

    public function cancelPaiementSession(Request $request) {
        $session = Session::where('monext_token', $request->token)->first();
        if ($session) {
            $datas = ['monext_token' => null, 'monext_link' => null, 'attente_paiement' => 0];
            $session->update($datas);
            if ($session->club_id) {
                return redirect()->route('clubs.formations')->with('error', "Votre paiement a été annulé");
            } else {
                return redirect()->route('urs.formations')->with('error', "Votre paiement a été annulé");
            }
        } else {
            return redirect()->route('clubs.formations')->with('error', "Le paiement a été annulé");
        }
    }

    public function attentePaiementValidationSession($session_is)
    {
        $session = Session::where('id', $session_is)->first();
        if ($session->club_id) {
            return redirect()->route('clubs.formations')->with('success', "Si vous avez procédé au paiement par virement de la prise en charge, celle-ci sera traitée d'ici quelques minutes et un email vous informera de sa prise en compte");
        } else {
            return redirect()->route('urs.formations')->with('success', "Si vous avez procédé au paiement par virement de la prise en charge, celle-ci sera traitée d'ici quelques minutes et un email vous informera de sa prise en compte");
        }
    }

    public function validationPaiement(Request $request)
    {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            $inscrit = Inscrit::where('monext_token', $request->token)->where('attente_paiement', 1)->first();
            if ($inscrit) {
                // on met à jour le flag attente_paiement à 0 pour l'inscrit
                $data = ['attente_paiement' => 0, 'status' => 1, 'secure_code' => null];
                $inscrit->update($data);
                $formation = $inscrit->session->formation;

                $personne = Personne::where('id', $inscrit->personne_id)->first();
                $personne->update(['creance' => 0]);
//                $personne->update(['avoir_formation' => 0]);

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

                $description = "Inscription à la formation " . $inscrit->session->formation->name;
                $ref = 'FORMATION-' . $inscrit->personne_id . '-' . $inscrit->session_id;
                $datai = ['reference' => $ref, 'description' => $description, 'montant' => $inscrit->amount, 'personne_id' => $inscrit->personne->id];
                $this->createAndSendInvoice($datai);
                return redirect()->route('formations.detail', $formation->id)->with('success', "Votre paiement a été pris en compte et vous êtes désormais inscrit à cette formation");
            }
        } else {
            return redirect()->route('formations.accueil')->with('error', "Votre paiement n'a pas été accepté");
        }
    }

    public function validationPaiementSession(Request $request)
    {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            $session = Session::where('monext_token', $request->token)->where('attente_paiement', 1)->first();
            if ($session) {
                // on met à jour le flag attente_paiement à 0 pour la session
                $data = ['attente_paiement' => 0, 'paiement_status' => 1];
                $session->update($data);

                $description = "Prise en charge de la session de formation ".$session->formation->name;
                $contact = null;
                if ($session->club_id) {
                    $ref = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
                    $datai = ['reference' => $ref, 'description' => $description, 'montant' => $session->pec, 'club_id' => $session->club_id];

                    // on récupère le contact du club
                    $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                        ->where('utilisateurs.clubs_id', $session->club_id)
                        ->where('fonctionsutilisateurs.fonctions_id', 97)
                        ->first();
                } else {
                    $ref = 'SESSION-FORMATION-'.$session->ur_id.'-'.$session->id;
                    $datai = ['reference' => $ref, 'description' => $description, 'montant' => $session->pec, 'ur_id' => $session->ur_id];

                    // on récupère le président UR
                    $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                        ->where('utilisateurs.urs_id', $session->ur_id)
                        ->where('fonctionsutilisateurs.fonctions_id', 57)
                        ->first();
                }
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
                if ($session->club_id) {
                    return redirect()->route('clubs.formations')->with('success', "Votre paiement pour la prise en charge de la session a bien été pris en compte");
                } else {
                    return redirect()->route('urs.formations')->with('success', "Votre paiement pour la prise en charge de la session a bien été pris en compte");
                }
            }
        } else {
            return redirect()->route('formations.accueil')->with('error', "Votre paiement n'a pas été accepté");
        }
    }

    public function attentePaiementValidation($formation_id)
    {
        return redirect()->route('formations.detail', $formation_id)->with('success', "Si vous avez procédé au paiement par virement de votre inscription, celle-ci sera traitée d'ici quelques minutes et un email vous informera de sa prise en compte");
    }

    public function payWithSecureCode($secure_code) {
        $user = session()->get('user');
        $inscrit = Inscrit::where('secure_code', $secure_code)->first();
        if (!$inscrit) {
            return redirect()->route('accueil');
        }
        if ($inscrit->personne_id != $user->id) {
            return redirect()->route('accueil');
        }

        return view('formations.paiement', compact('inscrit'));
    }

    public function evaluation($md5) {
        $session = Session::whereRaw("md5(id) = '$md5'")->first();
        if (!$session) {
            return redirect()->route('accueil')->with('error', "Le lien d'évaluation est désormais invalide");
        }

        // on regarde si une évaluation a déjà été faite pour cette session
        $user = session()->get('user');
        $evaluation = Evaluation::where('session_id', $session->id)->where('personne_id', $user->id)->first();
        if ($evaluation) {
            return redirect()->route('accueil')->with('error', "Vous avez déjà évalué cette formation");
        }

        // on regarde si la personne était inscrite à la sessiond e formation
        $inscrit = Inscrit::where('session_id', $session->id)->where('personne_id', $user->id)->first();
        if (!$inscrit) {
            return redirect()->route('accueil')->with('error', "Vous n'étiez pas inscrit à cette session de formation");
        }

        // on cherche les éléments de validation
        $themes = Evaluationstheme::orderBy('position')->get();
//        foreach ($themes as $theme) {
//            dd($theme->evaluationsitems->sortBy('position'));
//        }

        return view('formations.evaluation', compact('session', 'user', 'themes'));
    }

    public function saveEvaluation(Request $request, $personne_id, $session_id) {
        // on vérifie que la session existe*
        $session = Session::where('id', $session_id)->first();
        if (!$session) {
            return redirect()->route('accueil')->with('error', "La session de formation n'existe pas");
        }

        // on vérifie que la personne existe
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return redirect()->route('accueil')->with('error', "La personne n'existe pas");
        }

        $inscrit = Inscrit::where('session_id', $session->id)->where('personne_id', $personne->id)->first();
        if (!$inscrit) {
            return redirect()->route('accueil')->with('error', "Vous n'étiez pas inscrit à cette session de formation");
        }

        $evaluation = Evaluation::where('session_id', $session->id)->where('personne_id', $personne->id)->first();
        if ($evaluation) {
            return redirect()->route('accueil')->with('error', "Vous avez déjà évalué cette formation");
        }

        $total = 0; $nb = 0;
        foreach ($request->request as $k => $v) {
            $to_insert = 0;
            if ($k != '_token') {
                $data = ['session_id' => $session->id, 'personne_id' => $personne->id];
                list($type,$ind) = explode('_', $k);
                $data['evaluationsitem_id'] = $ind;
                if ($type == 'rangeeval') {
                    $to_insert = 1;
                    $data['stars'] = $v;
                    $total += $v;
                    $nb++;
                }
                if ($type == 'texteval' && $v != '' && $v != null) {
                    $to_insert = 1;
                    $data['comment'] = $v;
                }
                if ($to_insert) {
                    Evaluation::create($data);
                }
            }
        }

        // on calcule la moyenne
        $moyenne = round($total / $nb, 2);

        $formation = Formation::where('id', $session->formation_id)->first();
        if ($formation) {
            // on met à jour la note moyenne de la formation
            $total_formation = $formation->stars * $formation->reviews;
            $total_formation += $moyenne;
            $new_stars = round($total_formation / ($formation->reviews + 1), 2);
            $dataf = ['stars' => $new_stars, 'reviews' => $formation->reviews + 1];
            $formation->update($dataf);
        }
        return redirect()->route('accueil')->with('success', "Votre évaluation a bien été prise en compte");
    }

    public function listePublique() {
        $formations = Formation::where('published', 1)->orderByDesc('created_at')->get();
        foreach ($formations as $formation) {
            if (sizeof($formation->sessions->where('start_date', '>=', date('Y-m-d'))) > 0) {
                $formation->first_date = $formation->sessions->sortBy('start_date')->where('start_date', '>=', date('Y-m-d'))->first()->start_date;
            } else {
                $formation->first_date = '2222-01-01';
            }

            $formation->location = $this->getFormationCities($formation, $formation->location);
        }
        $formations = $formations->sortBy('first_date');

        return view('formations.liste-publique', compact('formations'));
    }

    public function detailPublique(Formation $formation) {
        foreach ($formation->sessions as $k => $session) {
            if ($session->club_id != '') {
                $club = Club::where('id', $session->club_id)->selectRaw('nom')->first();
                if ($club) {
                    $formation->sessions[$k]->nom_club = $club->nom;
                }
            }
        }
        $formation->location = strlen($formation->location) ? $formation->location : $this->getFormationCities($formation);
        return view('formations.detail-publique', compact('formation'));
    }

}
