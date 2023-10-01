<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmVote;
use App\Mail\RelanceReglement;
use App\Mail\SendAlertSupport;
use App\Mail\SendCodeForVote;
use App\Models\Candidat;
use App\Models\Election;
use App\Models\Formateur;
use App\Models\Motion;
use App\Models\Pays;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class FormationController extends Controller
{
    use Api;

    protected function getFormateur(Request $request)
    {
        $formateur = Formateur::where('id', $request->id)->first();
        $formateur->nom = $formateur->personne->nom;
        $formateur->prenom = $formateur->personne->prenom;
        if (!$formateur) {
            return new JsonResponse(['success' => 'OK'], 200);
        } else {
            return new JsonResponse(['success' => 'OK', 'personne' => $formateur], 200);
        }
    }

    protected function getReviews(Request $request)
    {
        $reviews = [];
        //TODO: création de la table reviews et des reviews associés à une formation
        //$reviews["liste"] = Reviews::where('id', $request->id)->first();

        $reviews["liste"] = [];

        if (!$reviews) {
            return new JsonResponse(['success' => 'OK'], 200);
        } else {
            return new JsonResponse(['success' => 'OK', 'reviews' => $reviews], 200);
        }
    }
}
