<?php

namespace App\Console\Commands;

use App\Concern\Invoice;
use App\Concern\Tools;
use App\Models\Souscription;
use Illuminate\Console\Command;

class ProcessFlorilege extends Command
{
    use Invoice;
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:florilege';

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
//        $souscriptions = Souscription::whereIn('id', [4260])->get();
////        $souscriptions = Souscription::whereIn('id', [4316, 4307])->get();
//        foreach ($souscriptions as $souscription) {
////            $data = ['statut' => 1];
////            $data = ['statut' => 1, 'monext_token' => null, 'monext_link' => null, 'ref_reglement' => 'Monext ' . $souscription->monext_token];
////            $souscription->update($data);
////            dd($souscription);
//
//            if ($souscription->personne_id) {
//                $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
//                $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'personne_id' => $souscription->personne_id];
//                $this->createAndSendInvoice($datai);
//            } else {
//                if ($souscription->clubs_id) {
//                    $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
//                    $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'club_id' => $souscription->clubs_id];
//                    $this->createAndSendInvoice($datai);
//                }
//            }
//        }
    }
}
