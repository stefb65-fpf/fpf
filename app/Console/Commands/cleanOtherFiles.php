<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\Rcompetition;
use Illuminate\Console\Command;

class cleanOtherFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:otherfiles';

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
        $year = date('Y') - 2;
        $competitions = Competition::where('saison', $year)->get();
        foreach ($competitions as $competition) {
            $dir = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/national/'.$year.'/compet'.$competition->numero;
            $this->cleanOldDir($dir);
        };

        $rcompetitions = Rcompetition::where('saison', $year)->get();
        foreach ($rcompetitions as $rcompetition) {
            $ur_id = str_pad($rcompetition->urs_id, 2, '0', STR_PAD_LEFT);
            $dir = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/regional/'.$year.'/UR'.$ur_id.'/compet'.$rcompetition->numero;
            $this->cleanOldDir($dir);
        };
    }

    private function cleanOldDir($dir)
    {
        $files = glob($dir.'/*');
        foreach ($files as $file) {
            if (!is_file($file)) {
                $tab_path = explode('/', $file);
                if ($tab_path[count($tab_path) - 1] == 'csv' || $tab_path[count($tab_path) - 1] == 'etiquettes') {
                    $this->cleanDir($file);
                    rmdir($file);
                }
            }
        }
    }

    private function cleanDir($dir)
    {
        $files = glob($dir.'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } else {
                $this->cleanDir($file);
                // on supprime le répertoire
                rmdir($file);
            }
        }
    }
}
