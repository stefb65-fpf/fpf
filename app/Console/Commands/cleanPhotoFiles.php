<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\Photo;
use App\Models\Rcompetition;
use App\Models\Rphoto;
use Illuminate\Console\Command;

class cleanPhotoFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:photofiles';

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
        $year = date('Y') - 3;
        $competitions = Competition::where('saison', $year)->where('numero', '!=', 99)->get();
        foreach ($competitions as $competition) {
            $dir = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/national/'.$year.'/compet'.$competition->numero;
            $photos = Photo::where('competitions_id', $competition->id)->get();
            foreach ($photos as $photo) {
                $file = $dir.'/'.$photo->ean.'.jpg';
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        };

        $rcompetitions = Rcompetition::where('saison', $year)->get();
        foreach ($rcompetitions as $rcompetition) {
            $ur_id = str_pad($rcompetition->urs_id, 2, '0', STR_PAD_LEFT);
            $dir = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/competitions/regional/'.$year.'/UR'.$ur_id.'/compet'.$rcompetition->numero;
            $photos = Rphoto::where('competitions_id', $rcompetition->id)->get();
            foreach ($photos as $photo) {
                $file = $dir.'/'.$photo->ean.'.jpg';
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}
