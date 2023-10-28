<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\Personne;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic;

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

    public function upload(Request $request, $formateur_id) {
        $formateur = Formateur::where('id', $formateur_id)->first();
        if (!$formateur) {
            return response()->json(['message' => "Le formateur n'existe pas.", 'success' => false], 200);
        }
        $type = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];
        if (!in_array($type, ['image/png', 'image/jpeg'])) {
            return response()->json(['message' => "Le fichier doit être au format image", 'success' => false], 200);
        }
        if ($size > 3000000) {
            return response()->json(['message' => "Le fichier ne doit pas dépasser 3 Mo.", 'success' => false], 200);
        }
        $dir = storage_path().'/app/public/uploads/formateurs/';
        $name = $formateur->id.'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $target_file = $dir.$name;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $sizes = getimagesize($target_file);
            $largeur = $sizes[0];
            $hauteur = $sizes[1];
            if ($largeur > $hauteur) {
                $largeur_need = null;
                $hauteur_need = min($hauteur, 150);
            } else {
                $largeur_need = min($largeur, 150);
                $hauteur_need = null;
            }
            ImageManagerStatic::make($target_file)->resize($largeur_need, $hauteur_need, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 70)->save($dir.'/'.$formateur->id.'_thumb.webp');
            $data = ['image' => $formateur->id.'_thumb.webp'];
            $formateur->update($data);
            return response()->json(['message' => "L'image a été chargé", 'success' => true], 200);
        } else {
            return response()->json(['message' => "Une erreur est survenue lors de l'upload du fichier.", 'success' => false], 200);
        }
    }
}
