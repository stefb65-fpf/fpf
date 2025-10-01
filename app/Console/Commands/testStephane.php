<?php

namespace App\Console\Commands;

use App\Concern\Invoice;
use App\Mail\SendUtilisateurCreateByAdmin;
use App\Mail\TestMail;
use App\Models\Club;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class testStephane extends Command
{
    use Invoice;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stephane';

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
        $club = Club::where('numero', 1639)->first();
        if (!$club) {
            return false;
        }
        $adresse = $club->adresse;
        $personne = null;

        // on crée le pdf facture d'avoir
        $primary_invoice = $datas['invoice'];
        $remboursements =  isset($datas['remboursements']) ? $datas['remboursements'] : [];
        $name = $numero.'.pdf';
        $dir = $invoice->getStorageDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chown($dir, 'www-data');
            chgrp($dir, 'www-data');
        }
        $pdf = App::make('dompdf.wrapper');
        $type = 'club';
        $pdf->loadView('pdf.avoir', compact('invoice', 'adresse', 'personne', 'club', 'primary_invoice', 'remboursements', 'type'))
            ->setWarnings(false)
            ->setPaper('a4', 'portrait')
            ->save($dir.'/'.$name);
        chown($dir.'/'.$name, 'www-data');
        chgrp($dir.'/'.$name, 'www-data');
    }
}
