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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait Invoice
{
    protected function createAndSendInvoice($datas) {
        $datai = ['reference' => $datas['reference'], 'description' => $datas['description'], 'montant' => $datas['montant']];
//        if (isset($datas['renew_club'])) {
//            $reglement = Reglement::where('reference', $datas['reference'])->first();
//            $configsaison = Configsaison::where('id', 1)->first();
//            $montant_florilege = $configsaison->prixflorilegefrance;
//            $tarif_abonne = Tarif::where('id', 17)->where('statut', 0)->first();
//            $tarif_abonne_non_adherent = Tarif::where('id', 19)->where('statut', 0)->first();
//
//            $utilisateurs = Utilisateur::join('reglementsutilisateurs', 'reglementsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
//                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
//                ->where('reglementsutilisateurs.reglements_id', $reglement->id)
//                ->selectRaw('personnes.nom, personnes.prenom, utilisateurs.identifiant, utilisateurs.ct, utilisateurs.statut, reglementsutilisateurs.adhesion, reglementsutilisateurs.abonnement, reglementsutilisateurs.florilege')
//                ->get();
//            $tab_adherents = [];
//            foreach ($utilisateurs as $utilisateur) {
//                list($tarif_adhesion, $tarif_adhesion_supp) = $this->getTarifByCt($utilisateur->ct);
//                $tab_adherents[] = [
//                    'nom' => $utilisateur->nom,
//                    'prenom' => $utilisateur->prenom,
//                    'identifiant' => $utilisateur->identifiant,
//                    'ct' => $utilisateur->ct,
//                    'adhesion' => $utilisateur->adhesion,
//                    'abonnement' => in_array($utilisateur->statut, [2,3]) ? $tarif_abonne : $tarif_abonne_non_adherent,
//                    'florilege' => $utilisateur->florilege > 0 ? round($montant_florilege * $utilisateur->florilege, 2) : 0
//                ];
//            }
//            dd($utilisateurs);
//
//        }
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
        $pdf->loadView('pdf.facture', compact('invoice', 'adresse', 'personne', 'club', 'ur'))
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
}
