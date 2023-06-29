<?php

namespace App\Http\Controllers;

use App\Concern\Hash;
use App\Concern\Tools;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Mail\SendEmailModifiedPassword;
use App\Mail\SendEmailReinitPassword;
use App\Models\Abonnement;
use App\Models\Commune;
use App\Models\Fonction;
use App\Models\Historique;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    use Tools;
    use Hash;

    /**
     * connexion d'un utilisateur au site, récupération des droits et redirectction vers son espace
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        $personne = Personne::where('email', $request->email)->first();
        if (!$personne) {
            return redirect()->route('login')->with('error', "Email incorrect");
        }
        unset($personne->password);

        // on doit déterminer les accès de l'utilisateur et les pousser dans la session
        $menu = [
            'club' => true,
            'ur' => false,
            'admin' => false,
            'formation' => true,
        ];
        if ($personne->email === 'stephane.closse@gmail.com') {
            $menu = [
                'club' => true,
                'ur' => true,
                'admin' => true,
                'formation' => false,
            ];
        }

        $request->session()->put('user', $personne);
        $request->session()->put('menu', $menu);
        if (!$personne->is_administratif) {
            $personne = $this->getSituation($personne);
        }

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        $action = 'Connexion au site';
        $this->registerAction($personne->id, 3, $action);

        return redirect()->route('accueil');
    }

    /**
     * autoconnexion d'un utilisateur au site, récupération des droits et redirectction vers son espace
     * @param Personne $personne
     * @return void
     */
    public function autologin(Personne $personne) {
//        $historiques = Historique::where()->paginate(25);
        unset($personne->password);
        if (!$personne->is_administratif) {
            $personne = $this->getSituation($personne);
        }
        request()->session()->put('user', $personne);

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        $action = 'Connexion au site';
        $this->registerAction($personne->id, 3, $action);

        return redirect()->route('accueil');
    }

    /**
     * Suppression de la session et déconnexion de l'utilisateur
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request) {
        $user = $request->session()->get('user');
        $action = 'Déconnexion du site';
        $this->registerAction($user->id, 3, $action);
        session()->forget('user');
        session()->forget('menu');
        return redirect()->route('login');
    }

    /*
     * envoi d'un mail pour réibnitialiser le mot de passe avec un lein crypté
     */
    public function sendResetAccountPasswordLink(SendResetLinkRequest $request)
    {
        $email = $request->email;
        $personne = Personne::where('email', $email)->first();

        if (!$personne) {
            return redirect()->route('forgotPassword')->with('error', "Nous ne trouvons pas votre email dans notre base de données.");
        }
        $crypt = $this->encodeShortReinit();
        $personne->secure_code = $crypt;
        $personne->save();

        $link = "https://fpf-new.federation-photo.fr/reinitPassword/" . $crypt;
        $mailSent = Mail::to($email)->send(new SendEmailReinitPassword($link));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $this->registerAction($personne->id, 3, "Demande génération mot de passe");

        $mail = new \stdClass();
        $mail->titre = "Demande de réinitialisation de mot de passe";
        $mail->destinataire = $email;
        $mail->contenu = $htmlContent;

        $this->registerMail($personne->id, $mail);

        return view('auth.linkSent', compact('link', 'email'));
    }

    /*
     * réinitialisation du mot de passe
     * @param $securecode
     */
    public function reinitPassword($securecode)
    {
        $personne = Personne::selectRaw('id, email')->where('secure_code', $securecode)->first();
        if (!$personne) {
            return redirect()->route('login')->with('error', "Ce lien n'est pas valide");
        }
        return view('auth.reinitPassword', compact("personne"));
    }

    /**
     * réinitialisation du mot de passe
     * @param ResetPasswordRequest $request
     * @param Personne $personne
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(ResetPasswordRequest $request, Personne $personne){
        $datap = array('password' => $this->encodePwd($request->password), 'secure_code' => null);
        $personne->update($datap);
        $this->registerAction(1, 4, "Modification du mot de passe");

        $mailSent = Mail::to($personne->email)->send(new SendEmailModifiedPassword());

        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = "Confirmation de modification de mot de passe";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;

        $this->registerMail($personne->id, $mail);

        $this->autologin($personne);
        return redirect()->route('accueil');
//        return redirect()->route('accueil')->with('success', "Votre mot de passe a été modifié avec succès");
    }



    public function registerAbonnement()
    {
        $communes = Commune::orderBy('nom')->get();
        return view('auth.registerAbonnement', compact('communes'));
    }

    protected function getSituation($personne) {
        if ($personne->is_adherent) {
            // on recherche les cartes actives
            $tab_cartes = [];
            $cartes_actives = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [2,3])->selectRaw('id, identifiant')->get();
            foreach ($cartes_actives as $carte) {
                $carte->actif = true;
                // on cherche les focntions de la carte
                $fonctions = Fonction::join('fonctionsutilisateurs', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
                    ->select('fonctions.id', 'fonctions.libelle')
                    ->where('fonctionsutilisateurs.utilisateurs_id', $carte->id)->get();
                $droits = []; $tab_fonctions = [];
                foreach ($fonctions as $fonction) {
                    if ($fonction->droits) {
                        foreach ($fonction->droits as $droit) {
                            $droits[] = $droit->label;
                        }
                    }
                    $tab_fonctions[] = array('id' => $fonction->id, 'libelle' => $fonction->libelle);
                }
                $carte->fonctions = $tab_fonctions;

                foreach ($carte->droits as $droit) {
                    $droits[] = $droit->label;
                }

                $carte->droits = $droits;
                $tab_cartes[] = $carte;
            }

            $cartes_inactives = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [0,4])->selectRaw('id, identifiant')->get();
            foreach ($cartes_inactives as $carte) {
                $carte->actif = false;
                $tab_cartes[] = $carte;
            }
            $personne->cartes = $tab_cartes;
        }

        if ($personne->is_abonne) {
            // on recherche son abonnement en cours
            $abonnement = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $personne->abonnement = $abonnement;
            }
        }

        if ($personne->is_formateur) {
            // on recherche les infos formateur
        }

        return $personne;
    }

//    protected function getDroits($personne)
//    {
//        $droits = [];
//        $droits['admin'] = $personne->admin;
//        $droits['gestionnaire'] = $personne->gestionnaire;
//        $droits['adherent'] = $personne->adherent;
//        $droits['membre'] = $personne->membre;
//        $droits['contributeur'] = $personne->contributeur;
//        $droits['visiteur'] = $personne->visiteur;
//        return $droits;
//    }

}
