<?php

namespace App\Http\Controllers;

use App\Concern\Tools;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Personne;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use Tools;

    public function __construct()
    {
        $this->middleware('checkLogin');
    }

    public function accueil()
    {
        return view('account.mon_compte');
    }

    public function monCompte()
    {
        return view('account.mon_compte');
    }
    public function monProfil()
    {
        return view('account.mon_profil');
    }
    public function mesActions()
    {
        $personne = session()->get('personne');
        $historiques= Historique::where('personne_id', $personne->id)->orderByDesc('created_at')
                ->paginate(4);
        return view('account.mes_actions', compact('historiques'));
    }
    public function mesFormations()
    {
        return view('account.mes_formations');
    }
    public function mesMails()
    {
        $personne = session()->get('personne');
        $mails = Historiquemail::where('personne_id', $personne->id)->orderByDesc('created_at')
            ->paginate(4);

        foreach ($mails as $mail) {
            $mail->contenu = $this->get_string_between($mail->contenu, '<body class="mail">', '</body>');
        }

        return view('account.mes_mails', compact('mails'));
    }

    public function formations()
    {
        return view('pages.formations');
    }

    public function gestionClub()
    {
        return view('pages.gestion_club');
    }

    public function gestionUr()
    {
        return view('pages.gestion_ur');
    }

    public function gestionFpf()
    {
        return view('pages.gestion_fpf');
    }
}
