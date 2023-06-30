<?php

namespace App\Http\Controllers;

use App\Models\Ur;
use Illuminate\Http\Request;

class UrController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'urAccess']);
    }

    public function gestion() {
        $ur = $this->getUr();
        return view('urs.gestion', compact('ur'));
    }

    public function infosUr() {
        $ur = $this->getUr();
        return view('urs.infos_ur', compact('ur'));
    }

    public function listeClubs() {
        $ur = $this->getUr();
        return view('urs.liste_clubs', compact('ur'));
    }

    public function listeAdherents() {
        $ur = $this->getUr();
        return view('urs.liste_adherents', compact('ur'));
    }

    public function listeFonctions() {
        $ur = $this->getUr();
        return view('urs.liste_fonctions', compact('ur'));
    }

    public function listeReversements() {
        $ur = $this->getUr();
        return view('urs.liste_reversements', compact('ur'));
    }

    protected function getUr() {
        $cartes = session()->get('cartes');
        if (!$cartes || count($cartes) == 0) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
        }
        $active_carte = $cartes[0];
        $ur_id = $active_carte->urs_id;
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
        }
        return $ur;
    }
}
