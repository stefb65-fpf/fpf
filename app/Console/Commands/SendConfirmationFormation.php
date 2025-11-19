<?php

namespace App\Console\Commands;

use App\Mail\ConfirmationDeroulementFormation;
use App\Mail\NoDecisionFormation;
use App\Models\Club;
use App\Models\Session;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendConfirmationFormation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:confirmation-formation';

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
        // on cherche les formations en distanciel qui ont lieu dans deux jours
        $start_date1 = date('Y-m-d', strtotime('2 day'));
        $start_date2 = date('Y-m-d', strtotime('10 day'));

        $sessions = Session::where(function ($query) use ($start_date1) {
            $query->where('type', 0)
                ->where('start_date', '<=', $start_date1)
                ->where('start_date', '>=', now());
        })
            ->orWhere(function ($query) use ($start_date2) {
                $query->where('type', 1)
                    ->where('start_date', '<=', $start_date2)
                    ->where('start_date', '>=', now());
            })
            ->whereIn('status', [0, 1])
            ->get();

        $datas = ['status' => 2];
        foreach ($sessions as $session) {
            // on regarde le statut de la session
            if ($session->status == 1) {
                // la session est confirmée, on envoie les mails
                $this->sendConfirmation($session);
                $session->update($datas);
            } else {
                Mail::mailer('smtp2')->to('formations@federation-photo.fr')->send(new NoDecisionFormation($session));
            }
        }
    }

    protected function sendConfirmation($session) {
        foreach ($session->formation->formateurs as $formateur) {
            if (filter_var($formateur->email, FILTER_VALIDATE_EMAIL)) {
                Mail::mailer('smtp2')->to($formateur->personne->email)->cc('formations@federation-photo.fr')->send(new ConfirmationDeroulementFormation($session));
            }
        }
        foreach ($session->inscrits->where('status', 1)->where('attente', 0) as $inscrit) {
            Mail::mailer('smtp2')->to($inscrit->personne->email)->send(new ConfirmationDeroulementFormation($session));
        }
        if ($session->club_id) {
            // on cherche le mail du contact du club
            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.clubs_id', $session->club->id)
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->first();
            if ($contact) {
                $send_mail = Mail::mailer('smtp2')->to($contact->personne->email);
                if ($session->club->courriel != '') {
                    $send_mail->cc($session->club->courriel);
                }
                $send_mail->send(new ConfirmationDeroulementFormation($session));
            }
        } elseif ($session->ur_id) {
            // on cherche le responsable formation de l'ur
            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.ur_id', $session->ur->id)
                ->where('fonctionsutilisateurs.fonctions_id', 65)
                ->first();

            // on cherche le président d'ur
            $president = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.ur_id', $session->ur->id)
                ->where('fonctionsutilisateurs.fonctions_id', 57)
                ->first();

            $send_mail = Mail::mailer('smtp2')->to($session->ur->courriel);
            if ($contact) {
                $send_mail->cc($contact->personne->email);
            }
            if ($president) {
                $send_mail->cc($president->personne->email);
            }
            $send_mail->send(new ConfirmationDeroulementFormation($session));
        }
    }
}
