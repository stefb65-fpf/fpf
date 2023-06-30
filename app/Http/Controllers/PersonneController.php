<?php

namespace App\Http\Controllers;

use App\Concern\Tools;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\CiviliteRequest;
use App\Models\Adresse;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Pays;
use App\Models\Personne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonneController extends Controller
{
    use Tools;

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


    public function updateCivilite(CiviliteRequest $request, Personne $personne)
    {
        $datap = array('nom' => $request->nom, 'prenom' => $request->prenom, 'datenaissance' => $request->datenaissance, "phone_mobile" => $request->phone_mobile);
        $personne->update($datap);
        $request->session()->put('user', $personne);
        $this->registerAction(1, 4, "Modification de vos informations de civilité");
        return redirect()->route('mon-profil')->with('success', "Vos informations de civilité ont été modifiées avec succès");
    }

    public function updateAdresse(AdressesRequest $request, Personne $personne, $form)
    {
        $selected_pays = Pays::where('id', $request->pays)->first();
        $datap_adresse = $request->all();
        unset($datap_adresse['_token']);
        unset($datap_adresse['_method']);
        unset($datap_adresse['enableBtn']);
       $datap_adresse['pays']=$selected_pays->nom;
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
//                dd($personne->adresses(), $personne->adresses[0]);
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

        $request->session()->put('user', $personne);
        $this->registerAction(1, 4, "Modification de vos adresses");
        return redirect()->route('mon-profil')->with('success', "Votre adresse a été modifiée avec succès");
    }




}