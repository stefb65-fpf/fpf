<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\Personne;
use Illuminate\Http\Request;

class FormateurController extends Controller
{
    public function checkTrainerEmail(Request $request) {
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => "L'adresse email saisie est invalide.", 'success' => false], 200);
        }

        $personne = Personne::where('email', $request->email)->first();
        if (!$personne) {
            return response()->json(['error' => "L'adresse email saisie ne correspond à aucune personne existante dans la base de données.", 'success' => false], 200);
        }

        // si la personne existe, on vérifie qu'elle n'est pas déjà formateur
        if ($personne->is_formateur) {
            return response()->json(['error' => "La personne saisie est déjà formateur.", 'success' => false], 200);
        }

        // sinon on passe la personne comme fomrateur
        $data = ['is_formateur' => 1];
        $personne->update($data);

        $dataf = ['personne_id' => $personne->id];
        $formateur = Formateur::create($dataf);

        $link = route('formateurs.edit', $formateur->id);
        return response()->json(['success' => true, 'link' => $link], 200);
    }
}
