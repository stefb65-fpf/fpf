<?php

namespace App\Http\Controllers\Api;


use App\Concern\Api;
use App\Concern\Hash;
use App\Concern\Tools;
use App\Exports\RoutageListAdherents;
use App\Http\Controllers\Controller;

use App\Mail\SendEmailReinitPassword;
use App\Mail\SendRenouvellementMail;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UtilisateurController extends Controller
{
    use Api;
    use Tools;
    use Hash;
    public function createListAdherents(Request $request)
    {
        $club = $request->club;
        $utilisateurs = Utilisateur::where('clubs_id', $club)->whereNotNull('personne_id')->orderBy('identifiant')->get();
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->personne->is_abonne) {
                $utilisateur->fin = isset($utilisateur->personne->abonnements->where('etat', 1)[0]) ?
                    $utilisateur->personne->abonnements->where('etat', 1)[0]->fin :
                    $utilisateur->personne->abonnements->where('etat', 1)[1]->fin;
            } else {
                $utilisateur->fin = '';
            }
        }
        $fichier = 'liste_adherents_' . date('YmdHis') . '.xls';
        if (Excel::store(new RoutageListAdherents($utilisateurs), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            return new JsonResponse(['file' => $file_to_download], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
        }
    }

    public function checkRenouvellementAdherents(Request $request) {
        list($montant_adhesion_club, $montant_abonnement_club, $montant_adhesion_club_ur) = $this->getMontantRenouvellementClub($request->club, $request->aboClub);
        list($tab_adherents, $total_adhesion, $total_abonnement) = $this->getMontantRenouvellementAdherents($request->adherents, $request->abonnes);
        $total_montant = $total_adhesion + $total_abonnement + $montant_abonnement_club + $montant_adhesion_club + $montant_adhesion_club_ur;
        return new JsonResponse(['adherents' => $tab_adherents,
            'total_adhesion' => $total_adhesion,
            'total_abonnement' => $total_abonnement,
            'total_montant' => $total_montant,
            'montant_abonnement_club' => $montant_abonnement_club,
            'montant_adhesion_club' => $montant_adhesion_club,
            'montant_adhesion_club_ur' => $montant_adhesion_club_ur
            ], 200);
    }

    public function validRenouvellementAdherents(Request $request) {
        $club = Club::where('id', $request->club)->first();
        list($montant_adhesion_club, $montant_abonnement_club, $montant_adhesion_club_ur) = $this->getMontantRenouvellementClub($request->club, $request->aboClub);
        list($tab_adherents, $total_adhesion, $total_abonnement) = $this->getMontantRenouvellementAdherents($request->adherents, $request->abonnes);
        $total_montant = $total_adhesion + $total_abonnement + $montant_abonnement_club + $montant_adhesion_club + $montant_adhesion_club_ur;
        $total_club = $montant_abonnement_club + $montant_adhesion_club + $montant_adhesion_club_ur;
        $total_adherents = $total_adhesion + $total_abonnement;
        // on supprime le règlement potentiellement en attente pour le club
        $old_reglement = Reglement::where('clubs_id', $club->id)->where('statut', 0)->first();
        if ($old_reglement) {
            DB::table('reglementsutilisateurs')->where('reglements_id', $old_reglement->id)->delete();
            $old_reglement->delete();
        }

        // on passe à 0 tous les adhérents du club dont le statut est à 1
        Utilisateur::where('clubs_id', $club->id)->where('statut', 1)->update(['statut' => 0]);

        // on crée un règlement  en indiquant si le club est concerné
        $ref_club = date('y').'-'.str_pad($club->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($club->numero, 4, '0', STR_PAD_LEFT);
        // on compte le nombre de reglemements pour ce club
        $nb_reglements = Reglement::where('reference', 'LIKE', $ref_club.'%')->count();
        $ref = $ref_club.'-'.str_pad($nb_reglements+1, 4, '0', STR_PAD_LEFT);

        $data = array('clubs_id' => $club->id, 'montant' => $total_montant, 'statut' => 0, 'reference' => $ref);
        if ($montant_adhesion_club > 0) {
            $data['adhClub'] = 1;
        }
        if ($montant_abonnement_club > 0) {
            $data['aboClub'] = 1;
        }
        $reglement = Reglement::create($data);

        // pour chaque adhérent, on passe le statut à 1 si l'adhésion est requise
        // on crée un règlement en indiquant l'abonnement et l'adhésion
        foreach ($tab_adherents as $adherent) {
            $datar = array('reglements_id' => $reglement->id, 'utilisateurs_id' => $adherent['adherent']['id']);
            if (isset($adherent['adhesion'])) {
                Utilisateur::where('id', $adherent['adherent']['id'])->update(['statut' => 1]);
                $datar['adhesion'] = 1;
            }
            if (isset($adherent['abonnement'])) {
                $datar['abonnement'] = 1;
            }
            DB::table('reglementsutilisateurs')->insert($datar);
        }

        // on crée le bordereau
        $name = $ref.'.pdf';
        $dir = $club->getImageDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.borderauclub', compact('tab_adherents', 'ref', 'club', 'total_montant', 'total_club',
            'montant_adhesion_club', 'montant_abonnement_club', 'montant_adhesion_club_ur', 'total_adhesion', 'total_abonnement', 'total_adherents'))
            ->setWarnings(false)
            ->setPaper('a4', 'portrait')
            ->save($dir.'/'.$name);
        list($tmp, $filename) = explode('htdocs/', $dir.'/'.$name);

        // on envoie le mail au contact du club
        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('clubs_id', $club->id)->where('fonctionsutilisateurs.fonctions_id', 97)->whereNotNull('utilisateurs.personne_id')->first();
        if ($contact) {
            $email = $contact->personne->email;
            $mailSent = Mail::to($email)->send(new SendRenouvellementMail($club, $dir.'/'.$name, $ref, $total_montant));
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

            $this->registerAction($contact->personne->id, 1, "Validation du bordereau pour renouvellement FPF");

            $mail = new \stdClass();
            $mail->titre = "Demande de renouvellement d'adhésion FPF";
            $mail->destinataire = $email;
            $mail->contenu = $htmlContent;
            $this->registerMail($contact->personne->id, $mail);
        }

        return new JsonResponse(['file' => $filename, 'reglement_id' => $reglement->id], 200);
    }


    public function checkBeforeInsertion(Request $request) {
        $personne = Personne::where('email', trim($request->email))->first();
        if ($personne) {
            return new JsonResponse(['code' => '10', 'personne' => $personne], 200);
        }
        $personnes = Personne::where('nom', trim($request->nom))->where('prenom', trim($request->prenom))->get();
        if (sizeof($personnes) > 0) {
            return new JsonResponse(['code' => '20', 'personnes' => $personnes], 200);
        }
        return new JsonResponse(['code' => '0'], 200);

    }

    public function getTarifForNewUser(Request $request) {
        $tarif = 0; $aboSupp = 0; $tarif_supp = 0;
        if ($request->type == 'adhesion') {
            list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($request->datenaissance);
            if ($tarif_supp > 0) {
                $aboSupp = 1;
            }
        }
        if ($request->type == 'abonnement') {
            $tarif = $this->getTarifAbonnement($request->pays);
        }

        if ($tarif == 0) {
            return new JsonResponse(['code' => '10'], 200);
        } else {
            return new JsonResponse(['code' => '0', 'tarif' => $tarif, 'aboSupp' => $aboSupp, 'tarifAboSupp' => $tarif_supp], 200);
        }
    }

    public function register(Request $request) {
        // on vérifie si la personne existe déjà
        if (!filter_var(trim($request->email), FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['erreur' => 'email invalide'], 400);
        }
        $personne = Personne::where('email', trim($request->email))->first();
        if ($personne) {
            return new JsonResponse(['erreur' => 'cette personne existe déjà'], 400);
        }
        // on enregistre la personne
        $password = $this->encodePwd($request->password);
        $datap = array('nom' => $request->nom, 'prenom' => $request->prenom, 'email' => trim($request->email), 'sexe' => $request->sexe,
            'phone_mobile' => $request->phone_mobile, 'password' => $password,
            'attente_paiement' => 1);
        if ($request->type == 'adhesion') {
            list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($request->datenaissance);
            if ($request->aboPlus == 1 || $ct == 2) {
                $datap['is_abonne'] = 1;
            }
            $datap['is_adherent'] = 1;
            $datap['datenaissance'] = $request->datenaissance;
            $datap['action_paiement'] = 'ADD_INDIVIDUEL';
        }
        if ($request->type == 'abonnement') {
            $datap['is_abonne'] = 1;
            $datap['action_paiement'] = 'ADD_ABONNEMENT';
        }
        $personne = Personne::create($datap);

        $this->insertWpUser($request->prenom, $request->nom, $trim($request->email), $request->password);

        // on enregistre l'adresse
        $pays = Pays::where('id', $request->pays)->first();
        if ($pays) {
            $dataa = array('libelle1' => $request->libelle1, 'libelle2' => $request->libelle2, 'codepostal' => $request->codepostal,
                'ville' => $request->ville, 'pays' => $pays->nom);
            $adresse = Adresse::create($dataa);

            // on lie l'adresse à la personne
            $personne->adresses()->attach($adresse->id);

        }


        $montant = 0;
        $ref = '';
        if ($request->type == 'adhesion') {
            list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($request->datenaissance);
            $montant = floatval($tarif);
            if ($request->aboPlus == 1) {
                $montant += floatval($tarif_supp);
            }
            $ref = 'ADH-NEW-'.$personne->id;
        }
        if ($request->type == 'abonnement') {
            $montant = floatval($this->getTarifAbonnement($request->pays));
            $ref = 'ABO-NEW-'.$personne->id;
        }
        $montant_cents = intval($montant * 100);

        // on redirige vers le paiement
        if ($request->paiement == 'bridge') {
            $url = 'https://api.bridgeapi.io/v2/payment-links';
            $transaction = new \stdClass();
            $transaction->amount = $montant;
            if ($transaction->amount == 0) {
                return new JsonResponse(['erreur' => 'impossible de récupérer le montant'], 400);
            }

            $transaction->currency = 'EUR';
            $transaction->label = $ref;

            $expired_date = new \DateTime(date('Y-m-d H:i:s'));
            $expired_date->add(new \DateInterval('P1D'));

            $bridge_datas = [
                "user" => [
                    "first_name" => $personne->prenom,
                    "last_name" => $personne->nom
                ],
                "expired_date" => $expired_date->format('c'),
                "client_reference" => strval($personne->id),
                "transactions" => [
                    $transaction
                ],
                "callback_url" => env('APP_URL') . "utilisateurs/attente_paiement_validation",
            ];


            list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
            if ($status == 200) {
                $reponse = json_decode($reponse);
                $personne->update(['bridge_id' => $reponse->id, 'bridge_link' => $reponse->url]);
                return new JsonResponse(['url' => $reponse->url], 200);
            } else {
                $personne->adresses()->detach();
                $this->deleteWpUser($personne->email);
                $personne->delete();
                $adresse->delete();
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        } else {
            // paiement CB
            $urls = [
                'cancelURL' => env('APP_URL') . "cancel_paiement",
                'returnURL' => env('APP_URL') . "validation_paiement_carte",
                'notificationURL' => env('APP_URL') . "personnes/notification_paiement",
            ];
            $user = [
                'email' => $personne->email,
                'prenom' => $personne->prenom,
                'nom' => $personne->nom,
            ];
            $result = $this->callMonext($montant_cents, $urls, $ref, $user);
            if ($result['code'] == '00000') {
                $personne->update(['monext_token' => $result['token'], 'monext_link' => $result['redirectURL']]);
                return new JsonResponse(['url' => $result['redirectURL']], 200);
            } else {
                $personne->adresses()->detach();
                $this->deleteWpUser($personne->email);
                $personne->delete();
                $adresse->delete();
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        }
    }

    public function renewIndividuel(Request $request) {
        $personne = Personne::where('id', $request->personne_id)->first();
        if (!$personne) {
            return new JsonResponse(['erreur' => 'Personne non trouvée'], 400);
        }
//        if ($personne->is_adherent !== 2) {
//            return new JsonResponse(['erreur' => 'Pas de renouvellement pour cet individuel'], 400);
//        }
        $utilisateur = Utilisateur::where('id', $request->carte)->first();
        if ($utilisateur->personne_id != $personne->id) {
            return new JsonResponse(['erreur' => 'Personne et carte non concordante'], 400);
        }

        // on récupère le montant dur enouvellement pour l'année en cours:
        list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($personne->datenaissance);
        $montant = $request->adhesion === 'adh' ? floatval($tarif) : floatval($tarif_supp) + floatval($tarif);
        if ($montant != $request->montant) {
            return new JsonResponse(['erreur' => 'Pas de renouvellement pour cet individuel'], 400);
        }
        $montant_cents = intval($montant * 100);
        $ref = 'ADH-REN-'.$utilisateur->identifiant;
        $last_reglement = Reglement::where('reference', 'LIKE', $ref.'%')->orderBy('id', 'DESC')->first();
        $num = $last_reglement ? intval(substr($last_reglement->reference, -4)) + 1 : 1;
        $ref .= '-'.str_pad($num, 4, '0', STR_PAD_LEFT);

        // on crée un règlement
        $reglement = Reglement::create(['montant' => $montant, 'reference' => $ref, 'statut' => 0]);

        // on crée la liaison reglements utilisateurs
        $dataru = array('reglements_id' => $reglement->id, 'utilisateurs_id' => $utilisateur->id, 'adhesion' => 1);
        if ($tarif_supp == 0 || $request->adhesion === 'all') {
            $dataru['abonnement'] = 1;
        }
        DB::table('reglementsutilisateurs')->insert($dataru);

        $datau = array('statut' => 1);
        $utilisateur->update($datau);


        if ($request->type == 'bridge') {
            $url = 'https://api.bridgeapi.io/v2/payment-links';
            $transaction = new \stdClass();
            $transaction->amount = $montant;
            $transaction->currency = 'EUR';
            $transaction->label = $ref;
            $expired_date = new \DateTime(date('Y-m-d H:i:s'));
            $expired_date->add(new \DateInterval('P1D'));

            $bridge_datas = [
                "user" => [
                    "first_name" => $personne->prenom,
                    "last_name" => $personne->nom
                ],
                "expired_date" => $expired_date->format('c'),
                "client_reference" => strval($personne->id),
                "transactions" => [
                    $transaction
                ],
                "callback_url" => env('APP_URL') . "utilisateurs/attente_paiement_validation",
            ];


            list($status, $reponse) = $this->callBridge($url, 'POST', json_encode($bridge_datas));
            if ($status == 200) {
                $reponse = json_decode($reponse);
                $reglement->update(['bridge_id' => $reponse->id, 'bridge_link' => $reponse->url]);
                return new JsonResponse(['url' => $reponse->url], 200);
            } else {
                $reglement->delete();
                DB::table('reglementsutilisateurs')->where('reglements_id', $reglement->id)->delete();
                $datau = array('statut' => 0);
                $utilisateur->update($datau);

                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        } else {
            $urls = [
                'cancelURL' => env('APP_URL') . "cancel_paiement_renew",
                'returnURL' => env('APP_URL') . "personnes/validation_paiement_carte_renew",
                'notificationURL' => env('APP_URL') . "reglements/notification_paiement",
            ];
            $user = [
                'email' => $personne->email,
                'prenom' => $personne->prenom,
                'nom' => $personne->nom,
            ];
            $result = $this->callMonext($montant_cents, $urls, $ref, $user);
            if ($result['code'] == '00000') {
                $reglement->update(['monext_token' => $result['token'], 'monext_link' => $result['redirectURL']]);
                return new JsonResponse(['url' => $result['redirectURL']], 200);
            } else {
                $reglement->delete();
                DB::table('reglementsutilisateurs')->where('reglements_id', $reglement->id)->delete();
                $datau = array('statut' => 0);
                $utilisateur->update($datau);
                return new JsonResponse(['erreur' => 'impossible de créer le lien de paiement'], 400);
            }
        }
    }

    protected function getMontantRenouvellementClub($club_id, $abo_club) {
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'impossible de récupérer le club'], 400);
        }
        $montant_adhesion_club = 0; $montant_abonnement_club = 0; $montant_adhesion_club_ur = 0;
        if ($club->statut !== 2) {
            // club non encore validé, on doit faire le renouvellement
            switch ($club->ct) {
                case 'C' :
                    $tarif_id = 3;
                    break;
                case 'A' :
                    $tarif_id = 2;
                    break;
                default :
                    $tarif_id = 1;
                    break;
            }
            $tarif = Tarif::where('id', $tarif_id)->where('statut', 0)->first();
            $montant_adhesion_club = $tarif->tarif;

            // montant de l'adhésion à l'ur
            $tarif = Tarif::where('id', 6)->where('statut', 0)->first();
            $montant_adhesion_club_ur = $tarif->tarif;

            if ($club->second_year == 1) {
                $montant_adhesion_club = $tarif->tarif / 2;
                $montant_adhesion_club_ur = $tarif->tarif / 2;
            }
        }
        if ($abo_club == 1) {
            $tarif = Tarif::where('id', 5)->where('statut', 0)->first();
            $montant_abonnement_club = $tarif->tarif;
        }
        return array($montant_adhesion_club, $montant_abonnement_club, $montant_adhesion_club_ur);
    }

    protected function getMontantRenouvellementAdherents($adherents, $abonnes) {
        $tab_adherents = []; $total_adhesion = 0; $total_abonnement = 0;
        if ($adherents) {
            foreach ($adherents as $adherent) {
                $utilisateur = Utilisateur::where('id', $adherent['id'])->first();
                if (!$utilisateur) {
                    return new JsonResponse(['erreur' => 'impossible de récupérer l\'utilisateur'], 400);
                }
                switch ($utilisateur->ct) {
                    case 3 :
                        $ct = '18 - 25 ans';
                        $tarif_id = 9;
                        break;
                    case 4 :
                        $ct = '<18 ans';
                        $tarif_id = 10;
                        break;
                    case 5 :
                        $ct = 'Famille';
                        $tarif_id = 11;
                        break;
                    case 6 :
                        $ct = 'Second club';
                        $tarif_id = 12;
                        break;
                    default :
                        $ct = '>25 ans';
                        $tarif_id = 8;
                        break;
                }
                $tarif = Tarif::where('id', $tarif_id)->where('statut', 0)->first();
                $line = ['prenom' => $utilisateur->personne->prenom, 'nom' => $utilisateur->personne->nom, 'identifiant' => $utilisateur->identifiant,
                    'ct' => $ct, 'id' => $utilisateur->id
                ];

                $tab_adherents[$utilisateur->identifiant]['adherent'] = $line;
                $tab_adherents[$utilisateur->identifiant]['adhesion'] = $tarif->tarif;
                $tab_adherents[$utilisateur->identifiant]['total'] = $tarif->tarif;
                $total_adhesion += $tarif->tarif;
            }
        }
        if ($abonnes) {
            $tarif_abonne = Tarif::where('id', 17)->where('statut', 0)->first();
            foreach ($abonnes as $abonne) {
                $utilisateur = Utilisateur::where('id', $abonne)->first();
                if (!$utilisateur) {
                    return new JsonResponse(['erreur' => 'impossible de récupérer l\'utilisateur'], 400);
                }
                if (!isset($tab_adherents[$utilisateur->identifiant])) {
                    $line = ['prenom' => $utilisateur->personne->prenom, 'nom' => $utilisateur->personne->nom, 'identifiant' => $utilisateur->identifiant,
                        'id' => $utilisateur->id];
                    $tab_adherents[$utilisateur->identifiant]['adherent'] = $line;
                    $tab_adherents[$utilisateur->identifiant]['total'] = $tarif_abonne->tarif;
                } else {
                    $tab_adherents[$utilisateur->identifiant]['total'] += $tarif_abonne->tarif;
                }
                $tab_adherents[$utilisateur->identifiant]['abonnement'] = $tarif_abonne->tarif;
                $total_abonnement += $tarif_abonne->tarif;
            }
        }
        ksort($tab_adherents);

        return array($tab_adherents, $total_adhesion, $total_abonnement);
    }
}
