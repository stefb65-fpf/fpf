<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Models\Personne;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    use Api;
    public function attentePaiementValidation() {
        return view('utilisateurs.attente_paiement_validation');
    }

    public function cancelPaiement(Request $request) {
        $personne = Personne::where('monext_token', $request->token)->first();
        if ($personne) {
            $personne->adresses()->detach();
            $personne->delete();
        }
        return view('auth/register');
    }

    public function validationPaiementCarte(Request $request) {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // TODO : on traite le règlement pour la personne

            // on regarde si c'est un adhérent

            // on regarde si la personn e est abonnér

            // on crée le règlement avec la ref passée au paiement

            // on evoie le mail pour cinfirmer l'inscription ou l'abonnement

            $code = 'ok';
        } else {
            $code = 'ko';
        }
        return view('utilisateurs.validation_paiement_carte', compact( 'code'));
        return view('utilisateurs.validation_paiement_carte');
    }
}
