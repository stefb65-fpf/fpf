<?php

namespace App\Http\Controllers;

use App\Concern\Hash;
use App\Concern\Tools;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Mail\SendEmailModifiedPassword;
use App\Mail\SendEmailReinitPassword;
use App\Models\Commune;
use App\Models\Historique;
use App\Models\Personne;
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
        $request->session()->put('personne', $personne);

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        $situation = $this->getSituation($personne);

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
        request()->session()->put('personne', $personne);

        $action = 'Connexion au site';
        $this->registerAction($personne->id, 3, $action);
    }

    /**
     * Suppression de la session et déconnexion de l'utilisateur
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request) {
        $personne = $request->session()->get('personne');
        $action = 'Déconnexion du site';
        $this->registerAction($personne->id, 3, $action);
        session()->forget('personne');
        return redirect()->route('login');
    }

    /*
     * envoi d'un mail pour réibnitialiser le mot de passe avec un lein crypté
     */
    public function sendResetAccountPasswordLink(SendResetLinkRequest $request)
    {
        $email = $request->email;
        $person = Personne::where('email', $email)->first();

        if (!$person) {
            return redirect()->route('forgotPassword')->with('error', "Nous ne trouvons pas votre email dans notre base de données.");
        }
        $crypt = $this->encodeShortReinit();
        $person->secure_code = $crypt;
        $person->save();

        $link = "https://fpf-new.federation-photo.fr/reinitPassword/" . $crypt;
        $mailSent = Mail::to($email)->send(new SendEmailReinitPassword($link));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $this->registerAction($person->id, 3, "Demande génération mot de passe");
        $this->registerMail($person->id, $email, "Demande de réinitialisation de mot de passe", $htmlContent);

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
        $this->registerMail($personne->id, $personne->email, "Confirmation de modification de mot de passe", $htmlContent);

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
            // on recherche les infos adhérents
            $utilisateurs = $personne->utilisateurs;
            $tab_users = [];
            foreach ($utilisateurs as $utilisateur) {
                $tab_users[] = $utilisateur;
            }
            dd($tab_users);
            $personne->cartes = $utilisateurs;
//            foreach ($utilisateurs as $utilisateur) {
//                $fonctions = DB::table('fonctionsutilisateurs')
//                    ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonction_id')
//                    ->where('utilisateur_id', $utilisateur->id)
//                    ->selectRaw('fonctions.id', 'fonctions.nom')
//                    ->get();
//
//            }
        }

        if ($personne->is_abonne) {
            // on recherche les infos abonnés
        }

        if ($personne->is_formateur) {
            // on recherche les infos formateur
        }
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
