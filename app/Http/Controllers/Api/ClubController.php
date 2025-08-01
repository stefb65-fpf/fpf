<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Exports\ColisageExport;
use App\Exports\ListeClubsExport;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ClubController extends Controller
{
    use Api;
    use Tools;

    public function clubActivite(Request $request, Club $club)
    {
        $club = Club::where('id', $request->club)->first();
        $club_activites = DB::table('activitesclubs')->where('clubs_id', $request->club)->get();
        $activites = [];
        $isInArray = false;
        foreach ($club_activites as $activite) {
            $activites[] = $activite->activites_id;
            if ($activite->activites_id == $request->clubPreferences) {
                $isInArray = true;
            }
        }
        if ($isInArray) {
            //on enleve la ligne correspondant de la table pivot
            DB::table('activitesclubs')->where('clubs_id', $request->club)->where('activites_id',$request->clubPreferences)->delete();
        } else {
            //on ajoute la ligne correspondant à la table pivot
            $data_ap = array('activites_id' => $request->clubPreferences, 'clubs_id' => $request->club);
            DB::table('activitesclubs')->insert($data_ap);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification des activités du club \"".$club->nom."\"");
        }
        return new JsonResponse(true, 200);
    }
    public function clubEquipement(Request $request, Club $club)
    {
        $club = Club::where('id', $request->club)->first();
        $club_equipements = DB::table('equipementsclubs')->where('clubs_id', $request->club)->get();
        $equipements = [];
        $isInArray = false;
        foreach ($club_equipements  as $equipement) {
            $equipements[] = $equipement->equipements_id;
            if ($equipement->equipements_id == $request->clubPreferences) {
                $isInArray = true;
            }
        }
        if ($isInArray) {
            //on enleve la ligne correspondant de la table pivot
            DB::table('equipementsclubs')->where('clubs_id', $request->club)->where('equipements_id',$request->clubPreferences)->delete();
        } else {
            //on ajoute la ligne correspondant à la table pivot
            $data_ap = array('equipements_id' => $request->clubPreferences, 'clubs_id' => $request->club);
            DB::table('equipementsclubs')->insert($data_ap);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification des équipements du club \"".$club->nom."\"");
        }
        return new JsonResponse(true, 200);
    }

    public function payByVirement(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement non trouvé'], 400);
        }
        $club = Club::where('id', $reglement->clubs_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'club non trouvé'], 400);
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->where('utilisateurs.clubs_id', $club->id)
            ->first();
        if (!$contact) {
            return new JsonResponse(['erreur' => 'contact non trouvé'], 400);
        }

        $url = 'https://api.bridgeapi.io/v2/payment-links';
        $transaction = new \stdClass();
        $transaction->amount = floatval($reglement->montant_paye);
        $transaction->currency = 'EUR';
        $transaction->label = 'FPF - '.$reglement->reference;

        $expired_date = new \DateTime(date('Y-m-d H:i:s'));
        $expired_date->add(new \DateInterval('P1D'));

        $bridge_datas = [
            "user" => [
                "first_name" => $contact->personne->prenom,
                "last_name" => $contact->personne->nom
            ],
            "expired_date" => $expired_date->format('c'),
            "client_reference" => $reglement->reference,
            "transactions" => [
                $transaction
            ],
            "callback_url" => env('APP_URL') . "clubs/attente_paiement_validation",
        ];

        list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
        if ($status == 200) {
            $reponse = json_decode($reponse);
            $reglement->update(['bridge_id' => $reponse->id, 'bridge_link' => $reponse->url]);
            return new JsonResponse(['url' => $reponse->url], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }

    public function payByCb(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement non trouvé'], 400);
        }
        $club = Club::where('id', $reglement->clubs_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'club non trouvé'], 400);
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->where('utilisateurs.clubs_id', $club->id)
            ->first();
        if (!$contact) {
            return new JsonResponse(['erreur' => 'contact non trouvé'], 400);
        }
        $montant = intval($reglement->montant_paye * 100);
        $urls = [
            'cancelURL' => env('APP_URL') . "clubs/reglements",
            'returnURL' => env('APP_URL') . "clubs/validation_paiement_carte",
            'notificationURL' => env('APP_URL') . "reglements/notification_paiement",
        ];
        $user = [
            'email' => $contact->personne->email,
            'prenom' => $contact->personne->prenom,
            'nom' => $contact->personne->nom,
        ];
        $result = $this->callMonext($montant, $urls, $reglement->reference, $user);
        if ($result['code'] == '00000') {
            $reglement->update(['monext_token' => $result['token'], 'monext_link' => $result['redirectURL']]);
            return new JsonResponse(['url' => $result['redirectURL']], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
        }
    }


    public function exportPdfClubsByDepts(): JsonResponse
    {
//        SELECT clubs.urs_id, clubs.nom, clubs.courriel, adresses.codepostal, adresses.ville, adresses.telephonemobile, adresses.telephonedomicile FROM clubs, adresses WHERE adresses.id = clubs.adresses_id AND clubs.statut = 2 ORDER BY adresses.codepostal ASC;
        $clubs = Club::join('adresses', 'clubs.adresses_id', '=', 'adresses.id')
            ->where('clubs.statut', 2)
            ->select('clubs.urs_id', 'clubs.nom', 'clubs.courriel', 'adresses.codepostal', 'adresses.ville', 'adresses.telephonemobile', 'adresses.telephonedomicile')
            ->orderBy('adresses.codepostal', 'ASC')
            ->get();
        foreach ($clubs as $club) {
            $codepostal = str_pad($club->codepostal, 5, '0', STR_PAD_LEFT);
            $club->codepostal = $codepostal;
            $club->dpt = substr($codepostal, 0, 2);
            $club->urs_id = str_pad($club->urs_id, 2, '0', STR_PAD_LEFT);
            $club->telephonemobile = $this->formatNumeroFrancais($club->telephonemobile);
            $club->telephonedomicile = $this->formatNumeroFrancais($club->telephonedomicile);
        }

        if ($clubs->isEmpty()) {
            return new JsonResponse(['error' => 'Aucun club trouvé'], 404);
        }

        $dir = storage_path().'/app/public/pdf';
        $name = 'clubs_'.date('YmdHis').'.pdf';
        $options = new Options();
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true); //
        $pdf = App::make('dompdf.wrapper');
        $pdf->getDomPDF()->setOptions($options);
        $pdf->loadView('pdf.liste_clubs', compact('clubs'))
            ->setWarnings(false)
            ->setPaper('a4', 'portrait')
            ->save($dir.'/'.$name);

        $file_to_download = env('APP_URL') . 'storage/app/public/pdf/'.$name;
        return new JsonResponse(['file' => $file_to_download], 200);
    }

    protected function formatNumeroFrancais($numero) {
        // Supprimer les points
        $numero = str_replace('.', '', $numero);

        // Remplacer l'indicatif international +33 par 0
        if (strpos($numero, '+33') === 0) {
            $numero = '0' . substr($numero, 3);
        }

        // Découper le numéro en paires de 2 chiffres
        $numero_formate = trim(chunk_split($numero, 2, ' '));

        return $numero_formate;
    }

    public function updateAffichagePhotoClub(Request $request) {
        $club = Club::where('id', $request->ref)->first();
        if (!$club) {
            return new JsonResponse(['error' => 'Club non trouvé'], 404);
        }

        $data = [
            'affichage_photo_club' => $request->affichage,
        ];
        $club->update($data);

        return new JsonResponse(['success' => true], 200);
    }

    public function updateClosedClub(Request $request)
    {
        $club = Club::where('id', $request->ref)->first();
        if (!$club) {
            return new JsonResponse(['error' => 'Club non trouvé'], 404);
        }

        $data = [
            'closed' => 1,
        ];
        $club->update($data);

        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user, "Modification du statut du club \"".$club->nom."\" (fermé : ".$request->closed.")");
        }

        return new JsonResponse(['success' => true], 200);
    }

    public function extractClubsForUr(Request $request): JsonResponse
    {
        $query = Club::orderBy('numero')->select('numero', 'nom', 'adresses_id', 'statut', 'id', 'courriel', 'ct', 'second_year')->where('urs_id', $request->ur);
        if ($request->statut != 'all') {
            $query->where('statut', $request->statut);
        }
        if ($request->typeCarte != 'all') {
            $query->where('ct', $request->typeCarte);
        }
        if ($request->abonnement != 'all') {
            $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
            if ($request->abonnement == 1) {
                $query->where('numerofinabonnement', '>=', $numeroencours);
            } else {
                $query->where('numerofinabonnement', '<', $numeroencours);
            }

        }
        $clubs = $query->get();
        foreach ($clubs as $k => $club) {
            // on récupère le contact du club
            $contact = Personne::join('utilisateurs', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
                ->select('personnes.id', 'personnes.nom', 'personnes.prenom', 'personnes.email')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $club->id)
                ->first();
            if (!$contact) {
                unset($clubs[$k]);
            } else {
                $club->contact = $contact;
            }
        }

        $fichier = 'liste_clubs_ur'.$request->ur.'_' . date('YmdHis') . '.xls';
        if (Excel::store(new ListeClubsExport($clubs), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            return new JsonResponse(['file' => $file_to_download], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
        }
    }
}
