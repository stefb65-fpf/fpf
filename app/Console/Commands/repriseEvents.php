<?php

namespace App\Console\Commands;

use App\Concern\Tools;
use App\Models\Reglement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class repriseEvents extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reprise:events';

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
//        $this->saveSouscriptionEvents(868);
//        die();
        // on vide la table des événements
        DB::table('histoevents')->truncate();

        // on remplit la table avec les règlements passés
        $reglements = Reglement::where('statut', 1)->orderBy('dateenregistrement')->get();
        foreach ($reglements as $reglement) {
            $this->saveReglementEvents($reglement->id);
        }
    }
}
