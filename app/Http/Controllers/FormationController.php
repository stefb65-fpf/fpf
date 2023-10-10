<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\FormationTools;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Mail\ConfirmationInscriptionFormation;
use App\Mail\SendInvoice;
use App\Models\Evaluation;
use App\Models\Evaluationstheme;
use App\Models\Formation;
use App\Models\Inscrit;
use App\Models\Personne;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FormationController extends Controller
{
    use FormationTools;
    use Api;
    use Invoice;
    use Tools;

    public function __construct()
    {
        $this->middleware('checkLogin');
    }

    public function accueil()
    {
        $formations = Formation::where('published', 1)->orderByDesc('created_at')->get();
        foreach ($formations as $formation) {
            $formation->location = $this->getFormationCities($formation, $formation->location);
        }

        return view('formations.accueil', compact('formations'));
    }

    public function detail(Formation $formation)
    {
        $user = session()->get('user');
        $personne = Personne::where('id', $user->id)->first();
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
                // on regarde si l'ur de la session est la même sque celui du user
                if ($user->cartes[0]->clubs_id != $session->club_id) {
                    $session_full = 1;
                }
            }
            $formation->sessions[$k]->full = $session_full;
        }
        $formation->location = strlen($formation->location) ? $formation->location : $this->getFormationCities($formation);
        return view('formations.detail', compact('formation', 'personne', 'inscriptions'));
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

                $email = $inscrit->personne->email;
                $mailSent = Mail::to($email)->send(new ConfirmationInscriptionFormation($inscrit->session));
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
                $datai = ['reference' => $ref, 'description' => $description, 'montant' => $inscrit->session->price, 'personne_id' => $inscrit->personne->id];
                $this->createAndSendInvoice($datai);
                return redirect()->route('formations.detail', $formation->id)->with('success', "Votre paiement a été pris en compte et vous êtes désormais inscrit à cette formation");
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

        // on regarde si une évlautaion a déjà été faite pour cette session
        $user = session()->get('user');
        $evaluation = Evaluation::where('session_id', $session->id)->where('personne_id', $user->id)->first();
        if ($evaluation) {
            return redirect()->route('accueil')->with('error', "Vous avez déjà évalué cette formation");
        }

        // on cherche les éléments de validation
        $themes = Evaluationstheme::orderBy('position')->get();
//        foreach ($themes as $theme) {
//            dd($theme->evaluationsitems->sortBy('position'));
//        }

        return view('formations.evaluation', compact('session', 'user', 'themes'));
    }

}
