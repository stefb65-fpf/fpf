<?php

namespace App\Console\Commands;

use App\Mail\RappelOrganisationFormation;
use App\Models\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

// TODO à mettre en cron une fois par jour
class SendRappelFormations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:rappel-formations';

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
        $start_date = date('Y-m-d', strtotime('21 day'));
        $sessions = Session::where('start_date', '=', $start_date)->get();
        foreach ($sessions as $session) {
            // on envoie un mail de rappel pour l'organisation de la formation
//            $dest = 'contact@envolinfo.com';
            $dest = 'formations@federation-photo.fr';
            Mail::mailer('smtp2')->to($dest)->send(new RappelOrganisationFormation($session));
        }
    }
}
