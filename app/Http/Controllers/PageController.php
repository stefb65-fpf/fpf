<?php

namespace App\Http\Controllers;

use App\Models\Historiquemail;
use App\Models\Personne;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct() {
        $this->middleware('checkLogin');
    }

    public function accueil() {

        return view('pages/welcome');
    }

    public function monCompte() {
//        $person = session()->get('personne');
//        dd($person);
//        $person = Personne::where('id',  )->first();
        return view('account.mon_compte');
    }
    public function mesMails() {
        $person = session()->get('personne');
        $mails = Historiquemail::where('personne_id', $person->id )->orderByDesc('created_at')
            ->paginate(2);;
        foreach ($mails as $mail){
            $mail->date = $mail->created_at->format('d/m/Y');
            $mail->hour = $mail->created_at->format('H:i');

        }
//        dd($mails);
        return view('account.mes_mails', compact('mails'));
    }
    public function formations() {
        return view('pages.formations');
    }

    public function gestionClub() {
        return view('pages.gestion_club');
    }

    public function gestionUr() {
        return view('pages.gestion_ur');
    }

    public function gestionFpf() {
        return view('pages.gestion_fpf');
    }
}
