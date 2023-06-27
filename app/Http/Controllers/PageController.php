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
        $person = session()->get('personne');
        return view('account.mon_compte', compact('person'));
    }

    public function monCompte()
    {
        $person = session()->get('personne');
//        dd($person);
//        $person = Personne::where('id',  )->first();
        return view('account.mon_compte', compact('person'));
    }
    public function monProfil()
    {
        $person = session()->get('personne');
//        $adress =

        return view('account.mon_profil', compact('person'));
    }
    public function mesActions()
    {
        $person = session()->get('personne');
        $actions= Historique::where('personne_id', $person->id)->orderByDesc('created_at')
                ->paginate(4);
        foreach ($actions as $action) {
            $action->date = $action->created_at->format('d/m/Y');
        }
        return view('account.mes_actions', compact('person', 'actions'));
    }
    public function mesFormations()
    {
        $person = session()->get('personne');

        return view('account.mes_formations', compact('person'));
    }
    public function mesMails()
    {
        $person = session()->get('personne');
        $mails = Historiquemail::where('personne_id', $person->id)->orderByDesc('created_at')
            ->paginate(4);
        foreach ($mails as $mail) {
            $mail->date = $mail->created_at->format('d/m/Y');
            $mail->hour = $mail->created_at->format('H:i');
            $mail->contenu = $this->get_string_between($mail->contenu, '<body class="mail">', '</body>');
        }

        return view('account.mes_mails', compact('mails','person'));
    }

    public function formations()
    {
        $person = session()->get('personne');
        return view('pages.formations', compact('person'));
    }

    public function gestionClub()
    {
        $person = session()->get('personne');
        return view('pages.gestion_club', compact('person'));
    }

    public function gestionUr()
    {
        $person = session()->get('personne');
        return view('pages.gestion_ur', compact('person'));
    }

    public function gestionFpf()
    {
        $person = session()->get('personne');
        return view('pages.gestion_fpf', compact('person'));
    }
}
