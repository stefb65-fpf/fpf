<?php

namespace App\Http\Controllers;

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
        return view('account/mon_compte');
    }

    public function formations() {
        return view('pages/formations');
    }

    public function gestionClub() {
        return view('pages/gestion_club');
    }

    public function gestionUr() {
        return view('pages/gestion_ur');
    }

    public function gestionFpf() {
        return view('pages/gestion_fpf');
    }
}
