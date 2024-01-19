<?php

namespace App\Console\Commands;

use App\Concern\Tools;
use App\Models\Session;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class sendEvaluationLink extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:evaluation';

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
        // on regarde si des sessions de formation se sont terminées la veille
        $yesterday = date('Y-m-d', strtotime('-3 day'));
        $sessions = Session::where(function (Builder $query) use ($yesterday) {
            $query->orWhere(function (Builder $query) use ($yesterday) {
                    $query->whereNull('end_date')
                        ->where('start_date', $yesterday);
                })
                ->orWhere(function (Builder $query) use ($yesterday) {
                    $query->whereNotNull('end_date')
                        ->where('end_date', $yesterday);
                })
            ;
        })->get();
        foreach ($sessions as $session) {
            // pour chaque inscrit, on envoie un mail avec le lien vers le formulaire d'évaluation de la formation
            foreach ($session->inscrits->where('status', 1) as $inscrit) {
                $email = $inscrit->personne->email;
                $mailSent = Mail::to($email)->send(new \App\Mail\SendEvaluationLink($session));
                $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                $sujet = "FPF // Lien d'évaluation pour la formation ".$session->formation->name;
                $mail = new \stdClass();
                $mail->titre = $sujet;
                $mail->destinataire = $email;
                $mail->contenu = $htmlContent;
                $this->registerMail($inscrit->personne->id, $mail);
            }
        }
    }
}
