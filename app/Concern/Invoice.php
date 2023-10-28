<?php


namespace App\Concern;


use App\Mail\SendInvoice;
use App\Mail\SendRenouvellementMail;
use App\Models\Club;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

trait Invoice
{
    protected function createAndSendInvoice($datas) {
        $datai = ['reference' => $datas['reference'], 'description' => $datas['description'], 'montant' => $datas['montant']];
        $adresse = null; $personne = null; $club = null;
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
            chown($dir.'/'.$name, 'www-data');
            chgrp($dir.'/'.$name, 'www-data');
        }
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.facture', compact('invoice', 'adresse', 'personne', 'club'))
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
            if ($club) {
                // on cherche le contact du club
                $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                    ->where('utilisateurs.clubs_id', $club->id)
                    ->where('fonctionsutilisateurs.fonctions_id', 97)
                    ->first();
                if ($contact) {
                    $user = session()->get('user');
                    $email = $contact->personne->email;
                    $mailSent = Mail::to($email)->cc($user->email)->send(new SendInvoice($invoice, $dir.'/'.$name));
                    $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                    $mail = new \stdClass();
                    $mail->titre = "Facture émise par la FPF";
                    $mail->destinataire = $email;
                    $mail->contenu = $htmlContent;
                    $this->registerMail($contact->personne->id, $mail);
                    $this->registerMail($user->id, $mail);
                }
            }
        }
        return true;
    }
}
