<?php

namespace App\Console\Commands;

use App\Models\Inscrit;
use Illuminate\Console\Command;

class checkInscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:inscription';

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
        $inscrits = Inscrit::where('secure_code', '!=', '')->where('attente_paiement', 1)->get();
        foreach ($inscrits as $inscrit) {
            $date = $inscrit->updated_at;
            $date->add(new \DateInterval('P2D'));
            if ($date->format('Y-m-d H:i:s') < date('Y-m-d H:i:s')) {
                $inscrit->update(['attente_paiement' => 0, 'attente' => 1, 'secure_code' => null]);
            }
        }
    }
}
