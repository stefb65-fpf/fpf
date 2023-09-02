<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RazUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raz:user';

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
//        $utilisateurs = DB::table('utilisateurs_save_after_v2')->where('statut', 10)->get();
//        foreach ($utilisateurs as $utilisateur) {
//            Utilisateur::where('id', $utilisateur->id)->update(['statut' => 10]);
//        }
    }
}
