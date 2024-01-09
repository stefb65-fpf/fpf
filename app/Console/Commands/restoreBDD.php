<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class restoreBDD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:bdd';

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
        die();
//        DB::connection('mysqlwp')->statement("DROP TABLE IF EXISTS test_sc");
        $file = storage_path('wp_federati.sql');
        if (isset($file)) {
            $fp = fopen($file, 'r');
            $skip = 0;
            $requete = '';
            $lines = explode("\n",file_get_contents($file));
            foreach ($lines as $line) {
                if ($skip > 26) {
                    if (str_starts_with($line, '--') || $line == "\n"  || $line == "") {
                        continue;
                    }
                    $requete .= $line;
                    if (str_ends_with(trim($line), ';')) {
                        if (str_starts_with($requete, 'INSERT INTO')) {
                            $tab_requete = explode(',(', $requete);
                            $nbreq = 0;
                            $debut = '';
                            foreach ($tab_requete as $req) {
                                $nbreq++;
                                if ($nbreq == 1) {
                                    list($debut, $fin) = explode('VALUES', $req);
                                    $sql_insert = $req;
                                } else {
                                    $sql_insert = $debut.' VALUES (' . $req;
                                }

                                try {
                                    DB::connection('mysqlwp')->statement($sql_insert);
                                } catch (\Exception $e) {
                                    var_dump($sql_insert);
                                }
                            }
                        } else {
                            try {
                                DB::connection('mysqlwp')->statement($requete);
                            } catch (\Exception $e) {
                                var_dump($requete);
                            }
                        }



//                        DB::connection('mysqlwp')->statement($requete);
                        $requete = '';
                    }
                }
                $skip++;
            }

//            while (($line = fgets($fp)) !== false) {
//                if ($skip > 26) {
//                    if (str_starts_with($line, '--') || $line == "\n") {
//                        continue;
//                    }
//                    dd(str_replace("\n", '', $line);
//                    dd(substr($line, -1, 1));
////                    DB::connection('mysqlwp')->statement($line);
//                }
//
//                if ($skip > 60) {
//                    die();
//                }
//
//                $skip++;
//            }
        } else {
            echo 'KO';
        }
    }
}
