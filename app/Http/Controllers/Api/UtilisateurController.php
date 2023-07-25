<?php

namespace App\Http\Controllers\Api;


use App\Exports\RoutageListAdherents;
use App\Http\Controllers\Controller;

use App\Mail\SendEmailReinitPassword;
use App\Mail\SendRenouvellementMail;
use App\Models\Adresse;
use App\Models\Club;
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

        $data = array('clubs_id' => $club->id, 'montant' => $total_montant, 'statut' => 0, 'moyenpaiement' => 0, 'reference' => $ref);
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
            // TODO : changer l'adresse email
//            $mail = $contact->personne->email;
            $mail = 'contact@envolinfo.com';
            Mail::to($mail)->send(new SendRenouvellementMail($club, $dir.'/'.$name, $ref, $total_montant));
        }

        return new JsonResponse(['file' => $filename], 200);
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
