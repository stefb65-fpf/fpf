<?php

namespace App\Console\Commands;

use App\Mail\SendRenouvellementMail;
use App\Models\Club;
use App\Models\Reglement;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestBascule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bascule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $photos = DB::table('photos')->get();
        $nb = 0;
        foreach ($photos as $photo) {
            $participant = str_replace('-', '', $photo->participants_id);
            $ean = substr($photo->ean, 0, 10);
            if ($participant != $ean) {

                $utilisateur = Utilisateur::where('identifiant', $photo->participants_id)->selectRaw('nom,prenom,courriel')->first();
                $identifiant_ean = substr($ean, 0, 2).'-'.substr($ean, 2, 4).'-'.substr($ean, 6, 4);
                $old_utilisateur = DB::table('utilisateurs_save_before_v2')->where('identifiant', $identifiant_ean)->selectRaw('nom,prenom,courriel')->first();
                if ($utilisateur && $old_utilisateur) {
                    if (trim($utilisateur->nom) != trim($old_utilisateur->nom) || trim($utilisateur->prenom) != trim($old_utilisateur->prenom) || $utilisateur->courriel != $old_utilisateur->courriel) {
                        // on cherche si l'utilisateur avec l'ancienne adresse email existe encore
//                        $existe_utilisateur = Utilisateur::where('courriel', $old_utilisateur->courriel)->first();
//                        if ($existe_utilisateur) {
                            echo $photo->participants_id.' '.$utilisateur->nom.' '.$utilisateur->prenom.' '.$utilisateur->courriel.' - '.
                                $photo->ean.' '.$old_utilisateur->nom.' '.$old_utilisateur->prenom.' '.$old_utilisateur->courriel."\n";
                            $nb++;
//                            $datar = ['participants_id' => $existe_utilisateur->identifiant];
//                            DB::table('photos')->where('id', $photo->id)->update($datar);
//                        }

                    }

                }
//                echo $photo->participants_id.' '.$utilisateur->nom.' '.$utilisateur->prenom.' '.$utilisateur->courriel.' - '.
//                    $photo->ean.' '.$old_utilisateur->nom.' '.$old_utilisateur->prenom.' '.$old_utilisateur->courriel."\n";
//                $nb++;
            }
        }
        echo 'nb : '.$nb."\n";
//        $reglement = Reglement::where('id', 35175)->first();
//        $ref = $reglement->reference;
//        $club = Club::where('id', $reglement->clubs_id)->first();
//        $name = $ref.'.pdf';
//        $dir = $club->getImageDir();
//        if (!is_dir($dir)) {
//            mkdir($dir, 0777, true);
//        }
//        $tarif_adhesion_club = Tarif::where('id', 4)->where('statut', 0)->first();
//        $montant_adhesion_club = $tarif_adhesion_club->tarif;
//        $tarif_adhesion_club_ur = Tarif::where('id', 7)->where('statut', 0)->first();
//        $montant_adhesion_club_ur = $tarif_adhesion_club_ur->tarif;
//        $tarif_abonnement_club = Tarif::where('id', 5)->where('statut', 0)->first();
//        $montant_abonnement_club = $tarif_abonnement_club->tarif;
//
//        $total_club = $montant_adhesion_club  + $montant_adhesion_club_ur;
//        if ($reglement->aboClub == 1) {
//            $total_club += $montant_abonnement_club;
//        } else {
//            $montant_abonnement_club = 0;
//        }
//        $total_montant = $total_club;
//
//        $tab_adherents = [];
//        $utilisateur = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
//            ->where('clubs_id', $club->id)->where('fonctionsutilisateurs.fonctions_id', 97)->first();
//
//        $tarif = Tarif::where('id', 8)->where('statut', 0)->first();
//        $line = ['prenom' => $utilisateur->personne->prenom, 'nom' => $utilisateur->personne->nom, 'identifiant' => $utilisateur->identifiant,
//            'ct' => '>25 ans', 'id' => $utilisateur->id, 'ctInt' => 2
//        ];
//
//        $tab_adherents[$utilisateur->identifiant]['adherent'] = $line;
//        $tab_adherents[$utilisateur->identifiant]['adhesion'] = $tarif->tarif;
//        $tab_adherents[$utilisateur->identifiant]['total'] = $tarif->tarif;
//        $total_adhesion = $tarif->tarif;
//
//        $total_abonnement = 0;
//        $reglement_utilisateur = DB::table('reglementsutilisateurs')->where('reglements_id', $reglement->id)->first();
//        if ($reglement_utilisateur) {
//            if ($reglement_utilisateur->abonnement == 1) {
//                $tarif = Tarif::where('id', 17)->where('statut', 0)->first();
//                $tab_adherents[$utilisateur->identifiant]['abonnement'] = $tarif->tarif;
//                $tab_adherents[$utilisateur->identifiant]['total'] += $tarif->tarif;
//                $total_abonnement += $tarif->tarif;
//            }
//        }
//        $total_adherents = $total_adhesion + $total_abonnement;
//        $total_montant += $total_adherents;
//
////        dd($reglement);
//
//        $pdf = App::make('dompdf.wrapper');
//        $pdf->loadView('pdf.borderauclub', compact('tab_adherents', 'ref', 'club', 'total_montant', 'total_club',
//            'montant_adhesion_club', 'montant_abonnement_club', 'montant_adhesion_club_ur', 'total_adhesion', 'total_abonnement', 'total_adherents'))
//            ->setWarnings(false)
//            ->setPaper('a4', 'portrait')
//            ->save($dir.'/'.$name);
//        list($tmp, $filename) = explode('htdocs/', $dir.'/'.$name);

        // on envoie le mail au contact du club
//        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
//            ->where('clubs_id', $club->id)->where('fonctionsutilisateurs.fonctions_id', 97)->whereNotNull('utilisateurs.personne_id')->first();
//        if ($contact) {
//            $email = $contact->personne->email;
//            $mailSent = Mail::to($email)->send(new SendRenouvellementMail($club, $dir.'/'.$name, $ref, $total_montant));
//            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
//
//
//            $mail = new \stdClass();
//            $mail->titre = "Demande de renouvellement d'adhésion FPF";
//            $mail->destinataire = $email;
//            $mail->contenu = $htmlContent;
//            $this->registerMail($contact->personne->id, $mail);
//        }
//        $utilisateurs = Utilisateur::whereNotNull('personne_id')->get();
//        foreach ($utilisateurs as $utilisateur) {
//            if ($utilisateur->personne->datenaissance) {
//                $date_naissance = new \DateTime($utilisateur->personne->datenaissance);
//                $date_now = new \DateTime();
//                $age = $date_now->diff($date_naissance)->y;
//                var_dump($age);
//                $ct = match ($age) {
//                    $age < 70 => 'cond 70',
//                    $age < 25 => 'cond 25',
//                    default => 'cond default',
//                };
//                var_dump($ct);
//            }
//        }
    }
}
