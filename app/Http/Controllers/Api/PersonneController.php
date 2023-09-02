<?php

namespace App\Http\Controllers\Api;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Personne;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonneController extends Controller
{
    use Tools;
    public function updateSession(Request $request)
    {
        $ref = $request->ref;
        $user = session()->get('user');
        $cartes = session()->get('cartes');
        foreach ($cartes as $k => $carte) {
            if ($carte->id == $ref) {
                unset($cartes[$k]);
                array_unshift($cartes, $carte);
            }
        }
        session()->put('cartes', $cartes);

        $menu_club = false;
        $menu_ur = false;
        $menu_admin = $user->is_administratif;
        $menu_formation = !$user->is_administratif;
        if (sizeof($cartes[0]->fonctions) > 0) {
            foreach ($cartes[0]->fonctions as $fonction) {
                if (in_array($fonction->id, config('app.club_functions'))) {
                    $menu_club = true;
                }
                if (in_array($fonction->id, config('app.ur_functions'))) {
                    $menu_ur = true;
                }
                if ($fonction->instance == 1) {
                    // on contrôle les droits liés à la fonction
                    if (sizeof($fonction->droits)) {
                        $menu_admin = true;
                    }
                }
            }
        }

        if (!$menu_admin) {
            if (sizeof($cartes[0]->droits) > 0) {
                $menu_admin = true;
            }
        }
        $menu = [
            'club' => $menu_club,
            'ur' => $menu_ur,
            'admin' => $menu_admin,
            'formation' => $menu_formation,
        ];
        session()->put('menu', $menu);

        return new JsonResponse(['success' => "changement de carte effectué"], 200);
    }

    public function getSession(Request $request)
    {
        $user = session()->get('user');
        $cartes = session()->get('cartes');
        $str_cartes = '';
        foreach ($cartes as $carte) {
            $str_cartes .= $carte->identifiant . ',';
        }
        $personne = Personne::where('id', $user->id)->selectRaw('password')->first();
        return new JsonResponse(['success' => "login concours", 'email' => $user->email, 'password' => $personne->password, 'cartes' => $str_cartes], 200);
    }

    public function setCookiesForNewsletter() {
        $menu = session()->get('menu');
        if ($menu['admin'] || $menu['ur']) {
            $cartes = session()->get('cartes');
            $identifiant = $cartes[0]->identifiant;
            $urs_id = $cartes[0]->urs_id;
            $tab_droits = []; // tableau des droits de l'utilisateur
            foreach($cartes[0]->droits as $droit) {
                $tab_droits[] = $droit->label;
            }
            $droit_news = 0;
            if (in_array('GESNEW', $tab_droits)) {
                setcookie('admin_newsletter_responsables_fpf', 1, time() + 3600, '/', 'federation-photo.fr', false, true);
                $droit_news = 1;
            }
            if (in_array('GESNEWCA', $tab_droits)) {
                setcookie('admin_newsletter_ca_fpf', 1, time() + 3600, '/', 'federation-photo.fr', false, true);
                $droit_news = 1;
            }
            if (in_array('GESNEWBU', $tab_droits)) {
                setcookie('admin_newsletter_bureau_fpf', 1, time() + 3600, '/', 'federation-photo.fr', false, true);
                $droit_news = 1;
            }
            if (in_array('GESNEWUR', $tab_droits)) {
                setcookie('admin_newsletter_responsables_ur', $urs_id, time() + 3600, '/', 'federation-photo.fr', false, true);
                $droit_news = 1;
            }
            if (in_array('GESNEWURCA', $tab_droits)) {
                setcookie('admin_newsletter_bureau_ur', $urs_id, time() + 3600, '/', 'federation-photo.fr', false, true);
                $droit_news = 1;
            }

            if ($droit_news == 1) {
                setcookie('admin_newsletter_fpf', $identifiant, time() + 3600, '/', 'federation-photo.fr', false, true);
            }

            return new JsonResponse(['success' => "cookies newsletter", 'droit_news' => $droit_news], 200);
        }

    }

    public function checkExternLogin(Request $request) {
        $personne = Personne::where('email', $request->email)->first();
        if (!$personne) {
            return new JsonResponse(['success' => 'KO', 'message' => "Email non présent dans notre base"], 200);
        }
        if (hash('sha512', env('SALT_KEY').$request->pass) !== $personne->password) {
            return new JsonResponse(['success' => 'KO', 'message' => "Mot de passe incorrect"], 200);
        }

        // on récupère les cartes utilisateurs
        list($menu, $cartes) = $this->getMenu($personne);

        return new JsonResponse(['success' => 'OK', 'cartes' => $cartes, 'personne' => $personne], 200);
    }

    public function checkExternLoginWithoutPaswword(Request $request) {
        $personne = Personne::where('email', substr($request->email, 7))->first();
        if (!$personne) {
            return new JsonResponse(['success' => 'KO', 'message' => "Email non présent dans notre base"], 200);
        }

        // on récupère les cartes utilisateurs
        list($menu, $cartes) = $this->getMenu($personne);

        return new JsonResponse(['success' => 'OK', 'cartes' => $cartes, 'personne' => $personne], 200);
    }

    public function getUserForAutoload(Request $request) {
        $personne =  $this->getUserFromWp($request->pass, $request->id);
        if (!$personne) {
            return new JsonResponse(['success' => 'OK'], 200);
        } else {
            return new JsonResponse(['success' => 'OK', 'personne' => $personne], 200);
        }
    }

    public function getStatus($term) {
        if (filter_var($term, FILTER_VALIDATE_EMAIL)) {
            $personne = Personne::where('email', $term)->first();
            $return = 0;
            if ($personne->is_adherent == 1) {
                $return = 1;
            } else {
                if ($personne->is_adherent == 2) {
                    foreach ($personne->utilisateurs as $utilisateur) {
                        if ($utilisateur->saison == date('Y')) {
                            $return = 1;
                        }
                    }
                }
            }
            return $return;
//            return $personne ? $personne->is_adherent : 0;
        } else {
            if (strlen($term) == 12) {
                $utilisateur = Utilisateur::where('identifiant', $term)->where('saison', date('Y'))->first();
                return $utilisateur ? 1 : 0;
            } else {
                return 0;
            }
        }
    }

    public function affectationUr(Request $request) {
        $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 400);
        }

        // on regarde le user max dans l'ur
        $max_utilisateur = Utilisateur::where('urs_id', $request->ur)->whereNull('clubs_id')->max('numeroutilisateur');
        $numero = $max_utilisateur ? $max_utilisateur + 1 : 1;
        $identifiant = $request->ur.'-0000-'.str_pad($numero, 4, '0', STR_PAD_LEFT);
        $utilisateur->update(['numeroutilisateur' => $numero, 'urs_id' => $request->ur, 'identifiant' => $identifiant]);
        return new JsonResponse(['success' => 'Utilisateur modifié'], 200);
    }

    public function isAdmin()
    {
        $isAdmin = (bool)session()->get('menu')['admin'];
        return new JsonResponse(['isAdmin' => $isAdmin], 200);

    }

    public function newsPreferences(Request $request)
    {
        $personne = Personne::where('id', $request->personne)->first();
        $datap = array('news' => $request->newspreference);
        $personne->update($datap);
//        $request->session()->put('user', $personne); session store not set on request...normal mais comment passer la session en ajax?
        $this->registerAction($personne->id, 4, "Modification de vos préférences concernant les nouvelles FPF");
        return [true];
    }
}
