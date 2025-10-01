<?php

namespace App\Console\Commands;

use App\Concern\Invoice;
use App\Concern\Tools;
use App\Models\Reglement;
use Illuminate\Console\Command;

class CheckInvoice extends Command
{
    use Tools;
    use Invoice;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invoice';

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
        $reglement = Reglement::where('id', 40859)->first();
        $this->saveInvoiceForReglement($reglement);

//        $description = "Avoir pour annulation de l'abonnement de l'utilisateur LACHAUD Denys pour la saison courante";
//        $datai = [
//            'reference' => 'ADH-NEW-ABO-20955-0001',
//            'description' => $description,
//            'montant' => 30,
//            'personne_id' => 12256,
//            'invoice' => $primary_invoice->numero,
//            'remboursements' => $tab_remboursements,
//        ];
//        $this->createAndSendAvoir($datai);
    }
}
