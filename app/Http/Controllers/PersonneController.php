<?php

namespace App\Http\Controllers;

use App\Concern\Hash;
use App\Concern\Tools;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\CiviliteRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\SendEmailModifiedPassword;
use App\Models\Adresse;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Pays;
use App\Models\Personne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PersonneController extends Controller
{
    use Tools;
    use Hash;

    public function __construct()
    {
        $this->middleware(['checkLogin', 'userAccessOnly']);
    }

    public function accueil()
    {
        return view('personnes.mon_compte');
    }

    public function monProfil()
    {
        $user = session()->get('user');
        $personne = Personne::where('id', $user->id)->first();
        $nbadresses = sizeof($personne->adresses);
        $personne->blacklist_date = date('d/m/Y',(strtotime($personne->blacklist_date)));
        if (!$nbadresses) {
            $personne->adresses[0] = [];
        } elseif ($nbadresses == 1) {
            $personne->adresses[1] = [];
        }
        foreach ( $personne->adresses as $adresse){
            if($adresse->pays){
                $country = Pays::where('nom', strtoupper(strtolower($adresse->pays)))->first();
                $adresse->indicatif =$country->indicatif;
//                dd( $adresse->indicatif);
            }else{
                $adresse->indicatif ="";
            }
        }
        $countries = Pays::all();
        return view('personnes.mon_profil', compact('personne', 'nbadresses', 'countries'));
    }

    public function mesActions()
    {
        $user = session()->get('user');
        $historiques = Historique::where('personne_id', $user->id)->orderByDesc('created_at')->paginate(50);
        return view('personnes.mes_actions', compact('historiques'));
    }

    public function mesFormations()
    {
        return view('personnes.mes_formations');
    }

    public function mesMails()
    {
        $user = session()->get('user');
        $mails = Historiquemail::where('personne_id', $user->id)->orderByDesc('created_at')->paginate(50);

        foreach ($mails as $mail) {
            $mail->contenu = $this->get_string_between($mail->contenu, '<body class="mail">', '</body>');
        }

        return view('personnes.mes_mails', compact('mails'));
    }

    public function updatePassword(ResetPasswordRequest $request, Personne $personne){
        $datap = array('password' => $this->encodePwd($request->password), 'secure_code' => null);
        $personne->update($datap);
        $request->session()->put('user', $personne);
        $this->registerAction($personne->id, 4, "Modification de votre mot de passe");

        $mailSent = Mail::to($personne->email)->send(new SendEmailModifiedPassword());

        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = "Confirmation de modification de mot de passe";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;

        $this->registerMail($personne->id, $mail);
        return redirect()->route('mon-profil')->with('success', "Votre mot de passe a été modifié avec succès");
    }

    public function updateCivilite(CiviliteRequest $request, Personne $personne)
    {
        $datap = array('nom' => $request->nom, 'prenom' => $request->prenom, 'datenaissance' => $request->datenaissance, "phone_mobile" => $request->phone_mobile);
        $personne->update($datap);
        $request->session()->put('user', $personne);
        $this->registerAction($personne->id, 4, "Modification de vos informations de civilité");
        return redirect()->route('mon-profil')->with('success', "Vos informations de civilité ont été modifiées avec succès");
    }

    public function updateAdresse(AdressesRequest $request, Personne $personne, $form)
    {
        $selected_pays = Pays::where('id', $request->pays)->first();
        $indicatif = $selected_pays->indicatif;
        $datap_adresse = $request->all();
        unset($datap_adresse['_token']);
        unset($datap_adresse['_method']);
        unset($datap_adresse['enableBtn']);
       $datap_adresse['pays']=$selected_pays->nom;
       if($datap_adresse["telephonedomicile"]){
           $datap_adresse["telephonedomicile"] = str_replace(" ","",$datap_adresse["telephonedomicile"]);
           $datap_adresse["telephonedomicile"] = ltrim($datap_adresse["telephonedomicile"], '0');
         $datap_adresse["telephonedomicile"] ='+'.$indicatif.'.'.$datap_adresse["telephonedomicile"];;
       }
//        dd($datap_adresse);
        if ($form == 1) {//$form = 1, c'est le formulaire d'adresse defaut / facturation
            if (!sizeof($personne->adresses)) { //la personne n'a aucune adresse en base. On en crée une.
                $new_adress = Adresse::create($datap_adresse);
                if ($new_adress) {
                    // on ajoute une ligne à la table pivot adresse_personne (le 'defaut' est à 1 pour "adresse de facturation"):

                    $data_ap = array('adresse_id' => $new_adress->id, 'personne_id' => $personne->id, 'defaut' => 1);
                    DB::table('adresse_personne')->insert($data_ap);
                }
            } else { //la personne a au moins une adresse en base. On met à jour l'adresse par defaut.
//                dd($datap_adresse, $personne->adresses[0]);
                $personne->adresses[0]->update($datap_adresse);
            }
        } else { //$form = 2, c'est le formulaire d'adresse de livraison
            if (sizeof($personne->adresses) == 2) { //la personne a déjà deux adresses (donc une de livraison). On la met à jour:
                $personne->adresses[1]->update($datap_adresse);
            } else { //la personne n'a pas encore d'adresse de livraison. On la crée:
                $new_adress = Adresse::create($datap_adresse);
                if ($new_adress) {
                    // on ajoute une ligne à la table pivot adresse_personne (le 'defaut' est à 2 pour "adresse de livraison"):
                    $data_ap = array('adresse_id' => $new_adress->id, 'personne_id' => $personne->id, 'defaut' => 2);
                    DB::table('adresse_personne')->insert($data_ap);
                }
//                dd($new_adress);
            }
        }
//dd($personne->id);
        $request->session()->put('user', $personne);
        $this->registerAction($personne->id, 4, "Modification de vos adresses");
        return redirect()->route('mon-profil')->with('success', "Votre adresse a été modifiée avec succès");
    }


}
