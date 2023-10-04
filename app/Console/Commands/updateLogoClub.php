<?php

namespace App\Console\Commands;

use App\Models\Club;
use Illuminate\Console\Command;

class updateLogoClub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:logoclub';

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
        $clubs = Club::get();
        foreach ($clubs as $club) {
            $logo = $club->logo;
            if ($logo) {
                // on regarde si le répertoire club existe
                $dir = storage_path() . '/app/public/uploads/clubs/' . $club->numero;
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $file = $dir.'/'.$logo;
                if (!file_exists($file)) {
                    // si le fichier n'existe pas, on le copie à partir de l'ancien répertoire
                    $old_file = '/home/vhosts/fpf.federation-photo.fr/htdocs/OLD/webroot/upload/logos/' . $logo;
                    if (file_exists($old_file)) {
                        copy($old_file, $file);
                    }
                }
            }
        }
    }
}
