<?php

namespace App\Http\Controllers;

use App\Concern\Hash;
use App\Concern\Tools;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Mail\SendEmailReinitPassword;
use App\Models\Commune;
use App\Models\Personne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    use Tools;
    use Hash;

    public function login(LoginRequest $request)
    {
        if ($request->email !== 'stephane.closse@gmail.com') {
            return redirect()->route('login')->with('error', "Email incorrect");
        }
        $user = array(
            'id' => 1,
            'name' => 'Closse',
            'firtsname' => 'Stéphane',
            'email' => 'stephane.closse@gmail.com'
        );
        $request->session()->put('user', $user);

        $action = 'lorem ipsume dolor sic emet';

        $this->registerAction(1, 4, "dfhsfhsfhs");
//        $user = array(
//            'id' => 1,
//            'name' => 'Closse',
//            'firtsname' => 'Stéphane',
//            'email' => 'stephane.closse@gmail.com'
//        );
//        session('user', $user);
//        $others = array(
//            'name' => 'titi',
//            'slug' => 'toto',
//        );
//        $request->session()->put('user', $user);
//        $request->session()->put('page', $others);
//
        return view('pages.welcome');
    }

    public function registerAbonnement()
    {
        $communes = Commune::orderBy('nom')->get();
        return view('auth.registerAbonnement', compact('communes'));
    }

    public function sendResetAccountPasswordLink(SendResetLinkRequest $request)
    {
        $email = $request->email;
        $person = Personne::where('email', $email)->first();

        if (!$person) {
            return redirect()->route('forgotPassword')->with('error', "Nous ne trouvons pas votre email dans notre base de données.");
        }
//       $pwd = $person->password;
        $crypt = $this->encodeShortReinit();
        $person->secure_code = $crypt;
        $person->save();
        $link = "https://fpf-new.federation-photo.fr/reinitPassword/" . $crypt . "/" . $email;
        dd($link);
//     Mail::to($email)->send(new SendEmailReinitPassword($link));// change permission on server ??? ErrorException -  Failed to open stream: Permission denied -
        $this->registerAction($person->id, 1, "Demande génération mot de passe");
        $content = "Nous avons reçu votre demande de réinitialisation de mot de passe.
            Pour le modifier , cliquez sur ce lien.
     Ce lien ne fonctionne pas ?
            Copiez ce lien dans votre barre de recherche: " . $link;
        $this->registerMail($person->id, $email, "Demande de réinitialisation de mot de passe", $content);
        return view('auth.linkSent', compact('link', 'email'));
    }

    public function reinitPassword($securecode = null, $email = null)
    {
        if (!$email || !$securecode) {
            return redirect()->route('login')->with('error', "Ce lien n'est pas valide");
        }

        return view('auth.reinitPassword', compact("email", "securecode"));
    }

    public function resetPassword(ResetPasswordRequest $request){
        $parameters = $request->query->all();
        $email = $parameters["mail"];
        $securecode = $parameters["securecode"];
        $personne = Personne::where('email', $email)->first();
        if (!$email || !$securecode || !$personne) {
            return redirect()->route('login')->with('error', "Ce lien n'est pas valide");
        }
        if ($personne->secure_code != $securecode) {
            return redirect()->route('login')->with('error', "Ce lien n'est pas valide");
        }
//        dd("c'est ok, on peut changer le pwd ", $request->password);
        $personne->password = $this->encodePwd($request->password);
        //on supprime le securecode pour que le lien ne puisse servir qu'une seule fois
        $personne->secure_code = null;
        $personne->save();
        //connexion à une session
        $user = array(
            'id' => $personne->id,
            'name' => $personne->nom,
            'firtsname' => $personne->prenom,
            'email' => $personne->email
        );
        $request->session()->put('user', $user);

        $this->registerAction(1, 4, "Modification du mot de passe");

        return view('pages.welcome')->with('success', "Votre mot de passe a été modifié avec succès");
    }

}
