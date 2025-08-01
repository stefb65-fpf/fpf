<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Models\Abonnement;
use App\Models\Configsaison;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilisateurController extends Controller
{
    use Api;
    use Tools;
    use Invoice;
    public function attentePaiementValidation() {
        return view('utilisateurs.attente_paiement_validation');
    }

    // affichage de la vue lors de l'annulation d'un paiement par carte pour une première adhésion FPF
    public function cancelPaiement(Request $request) {
        $personne = Personne::where('monext_token', $request->token)->first();
        if ($personne) {
            $personne->adresses()->detach();
            $this->deleteWpUser($personne->email);
            $personne->delete();
        }
        return view('auth/register');
    }


    // affichage de la vue lors de la validation d'un paiement par carte pour uen première adhésion FPF
    public function validationPaiementCarte(Request $request) {
        $result = $this->getMonextResult($request->token);
        $code = 'ko';
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            $personne = Personne::where('monext_token', $request->token)->first();
            if ($personne) {
                if ($personne->action_paiement == 'ADD_INDIVIDUEL') {
                    $description = "Adhésion individuelle à la FPF";
                } else {
                    $description = "Abonnement à la revue France Photo";
                }
                list($code, $reglement) = $this->saveNewPersonne($personne, 'Monext');

                if ($code == 'ok') {
                    $this->saveReglementEvents($reglement->id);
                    $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                    $this->createAndSendInvoice($datai);
                }
            }
        }
        return view('utilisateurs.validation_paiement_carte', compact( 'code'));
    }
}
