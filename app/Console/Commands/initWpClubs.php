<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class initWpClubs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:wpclubs';

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
        // on supprimer tous les clubs du site fédéral
        $wp_clubs = DB::connection('mysqlwp')->select("SELECT P.ID, M.meta_value FROM wp_posts P, wp_postmeta M WHERE M.post_id = P.id AND P.post_type = 'club-ur' AND M.meta_key = 'ptb_numero'");
        foreach ($wp_clubs as $wp_club) {
            // on supprime des tables wp_posts et wp_postmeta
            $post_id = $wp_club->ID;
            DB::connection('mysqlwp')->statement("DELETE FROM wp_posts WHERE ID = $post_id LIMIT 1");
            DB::connection('mysqlwp')->statement("DELETE FROM wp_postmeta WHERE post_id = $post_id");
        }
    }
}
