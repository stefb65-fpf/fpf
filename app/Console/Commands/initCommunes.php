<?php

namespace App\Console\Commands;

use App\Models\Commune;
use Illuminate\Console\Command;

class initCommunes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:communes';

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
        $communes = Commune::all();
        foreach ($communes as $commune) {
            if (strlen($commune->code_postal) < 5 || strlen($commune->numero_departement) < 2) {
                $data = array(
                    'code_postal' => str_pad($commune->code_postal, 5, 0, STR_PAD_LEFT),
                    'numero_departement' => str_pad($commune->numero_departement, 2, 0, STR_PAD_LEFT));
                $commune->update($data);
            }
        }
    }
}
