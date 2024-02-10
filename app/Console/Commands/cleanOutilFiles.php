<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\Photo;
use App\Models\Rcompetition;
use Illuminate\Console\Command;

class cleanOutilFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:outilfiles';

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
        $year = '2018';
//        $competitions = Competition::where('saison', $year)->get();
//        foreach ($competitions as $competition) {
//            $numero = str_pad($competition->numero, 2, '0', STR_PAD_LEFT);
//            $dir = '/home/vhosts/outils.federation-photo.fr/htdocs/outil-ip/'.$year.'/Compet'.$numero;
//            $photos = Photo::where('competitions_id', $competition->id)->get();
//            foreach ($photos as $photo) {
//                $file = $dir.'/'.$photo->ean.'.jpg';
//                if (file_exists($file)) {
//                    unlink($file);
//                }
//            }
//        };

        $rcompetitions = Rcompetition::where('saison', $year)->get();
        foreach ($rcompetitions as $rcompetition) {
            $dir = "/home/vhosts/outils.federation-photo.fr/htdocs/concours/UR".str_pad($rcompetition->urs_id, 2, '0', STR_PAD_LEFT)."/".$rcompetition->saison."_Compet".str_pad($rcompetition->numero, 2, '0', STR_PAD_LEFT)."/";
            dd($dir);
        }
    }
}
