<?php

namespace App\Http\Controllers;

use App\Concern\Tools;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\CiviliteRequest;
use App\Models\Adresse;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Personne;
use Illuminate\Http\Request;

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
//        dd($personne->adresses);
        $nbadresses = sizeof($personne->adresses);
        if (!$nbadresses) {
            $personne->adresses[0] = [];
        } elseif ($nbadresses == 1) {
            $personne->adresses[1] = [];
        }
//        dd($personne->adresses[1], $personne->adresses[0]);
        return view('personnes.mon_profil', compact('personne', 'nbadresses'));
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
//        dd($form);
        $datap_adresse = array(
            'libelle1' => $request->libelle1, 'libelle2' => $request->libelle2, 'libelle3' => $request->libelle3, 'codepostal' => $request->codepostal, 'ville' => $request->ville, 'pays' => $request->pays, 'telephonedomicile' => $request->telephonedomicile);


        if ($form === 1) {//$form = 1, c'est le formulaire d'adresse defaut / facturation
//            dd("form = 1");
            if (!sizeof($personne->adresses)) {
                Adresse::create($datap_adresse);
            } else {
                $personne->adresses[0]->update($datap_adresse);
            }
        }else{ //$form = 2, c'est le formulaire d'adresse de livraison
//            dd("form = 2");
//            dd(sizeof($personne->adresses));
            if (sizeof($personne->adresses) == 2) {
                dd("update livraison");
                $personne->adresses[1]->update($datap_adresse);
            } else {
//                dd("create livraison");
//                dd($datap_adresse);
                $new_adress = Adresse::create($datap_adresse);
                if($new_adress){
                    $data_ap = array('adresse_id'=>$new_adress->id,'personne_id'=> $personne->id,'defaut'=>2);
                    DB::table('adresse_personne')->insert($data_ap);

                }
//                Adresse::create($datap_adresse);
                dd($new_adress);
            }
        }

        $request->session()->put('user', $personne);
        $this->registerAction(1, 4, "Modification de vos adresses");
        return redirect()->route('mon-profil')->with('success', "Votre adresse a été modifiée avec succès");
    }


}
