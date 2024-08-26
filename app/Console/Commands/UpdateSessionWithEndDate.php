<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class UpdateSessionWithEndDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:inscription';

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
        // on récupère toutes les sessions de formation
        $sessions = Session::all();
        foreach ($sessions as $session) {
            // on soustrait un jour à la date de début de la session
            $date = date('Y-m-d', strtotime($session->start_date . ' -2 days'));
            $data = array('end_inscription' => $date);
            $session->update($data);
        }

    }
}
