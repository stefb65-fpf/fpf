<?php

namespace App\Http\Controllers;

use App\Concern\Tools;
use App\Http\Requests\CiviliteRequest;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Personne;
use Illuminate\Http\Request;

class PersonneController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'userAccessOnly']);
    }

    public function accueil() {
        return view('personnes.mon_compte');
    }

    public function monProfil() {
        $user = session()->get('user');
        $personne = Personne::where('id', $user->id)->first();
        return view('personnes.mon_profil', compact('personne'));
    }

    public function mesActions() {
        $user = session()->get('user');
        $historiques= Historique::where('personne_id', $user->id)->orderByDesc('created_at')->paginate(50);
        return view('personnes.mes_actions', compact('historiques'));
    }

    public function mesFormations() {
        return view('personnes.mes_formations');
    }

    public function mesMails() {
        $user = session()->get('user');
        $mails = Historiquemail::where('personne_id', $user->id)->orderByDesc('created_at')->paginate(50);

        foreach ($mails as $mail) {
            $mail->contenu = $this->get_string_between($mail->contenu, '<body class="mail">', '</body>');
        }

        return view('personnes.mes_mails', compact('mails'));
    }


    public function updateCivilite(CiviliteRequest $request, Personne $personne) {
        $datap = array('nom' => $request->nom, 'prenom' =>$request->prenom, 'datenaissance' => $request->datenaissance, "phone_mobile"=>$request->phone_mobile );
        $personne->update($datap);
        $request->session()->put('user', $personne);
        $this->registerAction(1, 4, "Modification de vos informations de civilité");
        return redirect()->route('mon-profil')->with('success', "Vos informations de civilité ont été modifiées avec succès");
    }
}
