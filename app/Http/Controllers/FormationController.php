<?php

namespace App\Http\Controllers;

use App\Concern\FormationTools;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    use FormationTools;

    public function __construct()
    {
        $this->middleware('checkLogin');
    }

    public function accueil()
    {
        $formations = Formation::where('published', 1)->orderByDesc('created_at')->get();
        foreach ($formations as $formation) {
//            dd($formation->categorie);
//            dd($formation->formateurs);
            $formation->location = strlen($formation->location) ? $formation->location : $this->getFormationCities($formation);
        }
        return view('formations.accueil', compact('formations'));
    }

    public function detail(Formation $formation)
    {
        $formation->location = strlen($formation->location) ? $formation->location : $this->getFormationCities($formation);
        return view('formations.detail', compact('formation'));
    }
}
