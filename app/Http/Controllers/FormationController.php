<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function __construct() {
        $this->middleware('checkLogin');
    }

    public function accueil() {
        $formations = Formation::where('published', 1)->orderByDesc('created_at')->get();
//        foreach ($formations as $formation) {
//            dd($formation->categorie);
//            dd($formation->formateurs);
//        }
        return view('formations.accueil', compact('formations'));
    }

    public function detail(Formation $formation) {

        return view('formations.detail', compact('formation'));
    }
}
