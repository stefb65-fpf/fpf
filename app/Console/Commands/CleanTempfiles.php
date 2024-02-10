<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanTempfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:tempfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyage des fichiers temp et des logs laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // on vide les fichier de log
        $file_fpf_log = '/home/vhosts/fpf.federation-photo.fr/htdocs/storage/logs/laravel.log';
        file_put_contents($file_fpf_log, '');

        $file_news_log = '/home/vhosts/newsletters.federation-photo.fr/htdocs/storage/logs/laravel.log';
        file_put_contents($file_news_log, '');

        $file_ur_log = '/home/vhosts/ur01.federation-photo.fr/htdocs/storage/logs/laravel.log';
        file_put_contents($file_ur_log, '');

        // on vide les répertoires temp sur copain
        $dir_copain = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/temp';
        $this->cleanDir($dir_copain);

        $dir_copain2 = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/json';
        $this->cleanDir($dir_copain2);

        $dir_copain3 = '/home/vhosts/copain.federation-photo.fr/htdocs/webroot/upload/ptemp';
        $this->cleanDir($dir_copain3);
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
