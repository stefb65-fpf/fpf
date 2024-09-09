<?php


namespace App\Concern;


use App\Mail\SendInvoice;
use App\Mail\SendRenouvellementMail;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Tarif;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait Invoice
{
    protected function createAndSendInvoice($datas) {
        $tabct = [
            2 => '> 25 ans',
            3 => '18 - 25 ans',
            4 => '< 18 ans',
            5 => 'famille',
            6 => '2nde carte',
        ];
        $datai = ['reference' => $datas['reference'], 'description' => $datas['description'], 'montant' => $datas['montant']];
        $tab_adherents = []; $tab_reglements = [];
        $renew_club = 0;
        if (isset($datas['renew_club'])) {
            $renew_club = 1;
            $reglement = Reglement::where('reference', $datas['reference'])->first();
            $tarif_florilege_france = Tarif::where('statut', 0)->where('id', 21)->first();
            $montant_florilege = $tarif_florilege_france->tarif;
            $tarif_abonne = Tarif::where('id', 17)->where('statut', 0)->first();
            $tarif_abonne_non_adherent = Tarif::where('id', 19)->where('statut', 0)->first();

            $utilisateurs = Utilisateur::join('reglementsutilisateurs', 'reglementsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('reglementsutilisateurs.reglements_id', $reglement->id)
                ->selectRaw('personnes.nom, personnes.prenom, utilisateurs.identifiant, utilisateurs.ct, utilisateurs.statut, reglementsutilisateurs.adhesion, reglementsutilisateurs.abonnement, reglementsutilisateurs.florilege')
                ->get();
            foreach ($utilisateurs as $utilisateur) {
                $tarif_adhesion = $this->getTarifByCtClub($utilisateur->ct);
                $tab_adherents[] = [
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'identifiant' => $utilisateur->identifiant,
                    'ct' => $tabct[$utilisateur->ct],
                    'adhesion' => $utilisateur->adhesion * $tarif_adhesion,
                    'abonnement' => $utilisateur->abonnement == 1 ? (in_array($utilisateur->statut, [2,3]) ? $tarif_abonne->tarif : $tarif_abonne_non_adherent->tarif) : 0,
//                    'abonnement' => in_array($utilisateur->statut, [2,3]) ? $tarif_abonne : $tarif_abonne_non_adherent,
                    'florilege' => $utilisateur->florilege > 0 ? round($montant_florilege * $utilisateur->florilege, 2) : 0
                ];
            }

            list($montant_adhesion_club, $montant_abonnement_club, $montant_adhesion_club_ur, $montant_florilege_club) = $this->getMontantRenouvellementClub2($reglement->clubs_id, $reglement->aboClub, $reglement->florilegeClub);
            if ($reglement->adh_club == 0) {
                $montant_adhesion_club = 0;
                $montant_adhesion_club_ur = 0;
            }
            $total_club = $montant_adhesion_club + $montant_abonnement_club + $montant_adhesion_club_ur + $montant_florilege_club;

            $montant_adhesion_adherents = 0; $montant_abonnement_adherents = 0; $montant_florilege_adherents = 0;
            foreach ($tab_adherents as $k => $adherent) {
                $montant_adhesion_adherents += $adherent['adhesion'];
                $montant_abonnement_adherents += $adherent['abonnement'];
                $montant_florilege_adherents += $adherent['florilege'];
                $tab_adherents[$k]['total'] = $adherent['adhesion'] + $adherent['abonnement'] + $adherent['florilege'];
            }
            $total_adherents = $montant_adhesion_adherents + $montant_abonnement_adherents + $montant_florilege_adherents;
            $tab_reglements['montant_adhesion_club'] = $montant_adhesion_club;
            $tab_reglements['montant_abonnement_club'] = $montant_abonnement_club;
            $tab_reglements['montant_adhesion_club_ur'] = $montant_adhesion_club_ur;
            $tab_reglements['montant_florilege_club'] = $montant_florilege_club;
            $tab_reglements['total_club'] = $total_club;
            $tab_reglements['montant_adhesion_adherents'] = $montant_adhesion_adherents;
            $tab_reglements['montant_abonnement_adherents'] = $montant_abonnement_adherents;
            $tab_reglements['montant_florilege_adherents'] = $montant_florilege_adherents;
            $tab_reglements['montant_total_adherents'] = $montant_florilege_adherents + $montant_abonnement_adherents + $montant_adhesion_adherents;
        }
        $adresse = null; $personne = null; $club = null; $ur = null;
        if (isset($datas['personne_id'])) {
            $personne = Personne::where('id', $datas['personne_id'])->first();
            if (!$personne) {
                return false;
            }
            $datai['personne_id'] = $personne->id;
            $adresse = $personne->adresses()->first();
        } else {
            if (isset($datas['club_id'])) {
                $club = Club::where('id', $datas['club_id'])->first();
                if (!$club) {
                    return false;
                }
                $datai['club_id'] = $club->id;
                $adresse = $club->adresse;
            } else {
                if (isset($datas['ur_id'])) {
                    $ur = Ur::where('id', $datas['ur_id'])->first();
                    if (!$ur) {
                        return false;
                    }
                    $datai['ur_id'] = $ur->id;
                    $adresse = $ur->adresse;
                }
            }
        }
        $ref = date('y').'-'.date('m');
        $last_invoice = \App\Models\Invoice::where('numero', 'LIKE', $ref.'%')->orderBy('id', 'DESC')->first();
        $num = $last_invoice ? intval(substr($last_invoice->numero, -4)) + 1 : 1;
        $numero = $ref.'-'.str_pad($num, 4, '0', STR_PAD_LEFT);
        $datai['numero'] = $numero;
        $invoice = \App\Models\Invoice::create($datai);

        // on crée le pdf facture
        $name = $numero.'.pdf';
        $dir = $invoice->getStorageDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chown($dir, 'www-data');
            chgrp($dir, 'www-data');
        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.facture', compact('invoice', 'adresse', 'personne', 'club', 'ur', 'tab_adherents', 'tab_reglements', 'renew_club'))
            ->setWarnings(false)
            ->setPaper('a4', 'portrait')
            ->save($dir.'/'.$name);
        chown($dir.'/'.$name, 'www-data');
        chgrp($dir.'/'.$name, 'www-data');


        if ($personne) {
            $email = $personne->email;
            $mailSent = Mail::to($email)->send(new SendInvoice($invoice, $dir.'/'.$name));
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

            $mail = new \stdClass();
            $mail->titre = "Facture émise par la FPF";
            $mail->destinataire = $email;
            $mail->contenu = $htmlContent;
            $this->registerMail($personne->id, $mail);
        } else {
            $contact = null;
            if ($club) {
                // on cherche le contact du club
                $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                    ->where('utilisateurs.clubs_id', $club->id)
                    ->where('fonctionsutilisateurs.fonctions_id', 97)
                    ->first();

            } else {
                if ($ur) {
                    $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                        ->where('utilisateurs.urs_id', $ur->id)
                        ->where('fonctionsutilisateurs.fonctions_id', 57)
                        ->first();
                }
            }
            if ($contact) {
                $email = $contact->personne->email;
                $user = session()->get('user');
                if ($user) {
                    $mailSent = Mail::to($email)->cc($user->email)->send(new SendInvoice($invoice, $dir.'/'.$name));
                } else {
                    $mailSent = Mail::to($email)->send(new SendInvoice($invoice, $dir.'/'.$name));
                }

                $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                $mail = new \stdClass();
                $mail->titre = "Facture émise par la FPF";
                $mail->destinataire = $email;
                $mail->contenu = $htmlContent;
                $this->registerMail($contact->personne->id, $mail);
                if ($user) {
                    $this->registerMail($user->id, $mail);
                }
            }
        }
        return true;
    }



    protected function getMontantRenouvellementClub2($club_id, $abo_club, $florilege_club) {
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'impossible de récupérer le club'], 400);
        }
        $montant_adhesion_club = 0; $montant_abonnement_club = 0; $montant_adhesion_club_ur = 0; $renew_old = 0; $montant_florilege = 0;

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
        if ($club->second_year == 1) {
            $montant_adhesion_club = $tarif->tarif / 2;
        }

        // montant de l'adhésion à l'ur
        $tarif = Tarif::where('id', 6)->where('statut', 0)->first();
        $montant_adhesion_club_ur = $tarif->tarif;
        if ($club->second_year == 1) {
            $montant_adhesion_club_ur = $tarif->tarif / 2;
        }
        if ($abo_club == 1) {
            $tarif = Tarif::where('id', 5)->where('statut', 0)->first();
            $montant_abonnement_club = $tarif->tarif;
        }

        if ($florilege_club > 0) {
            $tarif_florilege_france = Tarif::where('statut', 0)->where('id', 21)->first();
            $prix_florilege = $tarif_florilege_france->tarif;
            $montant_florilege = round($florilege_club * $prix_florilege, 2);
        }
        return array($montant_adhesion_club, $montant_abonnement_club, $montant_adhesion_club_ur, $montant_florilege);
    }


    protected function getTarifByCtClub($ct)
    {
        $tarif_id = match ($ct) {
            '3' => 9,
            '4' => 10,
            '5' => 11,
            '6' => 12,
            default => 8,
        };
        $tarif_adhesion = Tarif::where('statut', 0)->where('id', $tarif_id)->first();
        return $tarif_adhesion ? $tarif_adhesion->tarif : 0;
    }
}
