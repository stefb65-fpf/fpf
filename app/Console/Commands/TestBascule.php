<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;

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
        $utilisateurs = Utilisateur::whereNotNull('personne_id')->get();
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->personne->datenaissance) {
                $date_naissance = new \DateTime($utilisateur->personne->datenaissance);
                $date_now = new \DateTime();
                $age = $date_now->diff($date_naissance)->y;
                var_dump($age);
                $ct = match ($age) {
                    $age < 70 => 'cond 70',
                    $age < 25 => 'cond 25',
                    default => 'cond default',
                };
                var_dump($ct);
            }
        }
    }
}
