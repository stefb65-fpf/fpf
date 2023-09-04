<?php

namespace App\Console\Commands;

use App\Concern\Tools;
use App\Models\Reglement;
use Illuminate\Console\Command;

class CheckInvoice extends Command
{
    use Tools;
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
        $reglement = Reglement::where('id', 35144)->first();
        $this->saveInvoiceForReglement($reglement);
    }
}
