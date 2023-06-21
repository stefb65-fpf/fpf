<?php

namespace App\Console\Commands;

use App\Models\Adresse;
use App\Models\Commune;
use App\Models\Pays;
use Illuminate\Console\Command;
use function PHPUnit\Framework\isNan;

class initAdresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:adresses';

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
        $adresses = Adresse::all();
        $pays = Pays::all();
        $tab_pays = [];
        foreach ($pays as $pay) {
            $tab_pays[mb_strtolower($pay->nom)] = $pay->indicatif;
        }


        // traitement à exécuter en premier passage
//        foreach ($adresses as $adresse) {
//            if ($adresse->pays == '') {
//                $data = array('pays' => 'France');
//                $adresse->update($data);
//            } else {
//                if  ($adresse->pays != trim($adresse->pays)) {
//                    $data = array('pays' => trim($adresse->pays));
//                    $adresse->update($data);
//                }
//            }
//        }

//        -------------------------------------------------------------------------------------------------

        // traitement à excuter en second passage pour marquer les adresses incorrects
//        foreach ($adresses as $adresse) {
//            $pays = mb_strtolower($adresse->pays);
//            if ($pays == 'deutschland') {
//                $data = ['pays' => 'Allemagne'];
//                $adresse->update($data);
//                $pays = 'allemangne';
//            }
//            if ($pays == '') {
//                $data = ['erreur' => 10];
//                $adresse->update($data);
//            } else {
//                if (!array_key_exists($pays, $tab_pays)) {
//                    // on regarde si le code postal existe dans la table communes
//                    $code_postal = $adresse->codepostal;
//                    $commune = Commune::where('code_postal', $code_postal)->first();
//                    if ($commune) {
//                        $data = ['pays' => 'France', 'erreur' => 11];
//                    } else {
//                        $data = ['erreur' => 9];
//                    }
//                    $adresse->update($data);
//
//                }
//            }
//        }

//        -------------------------------------------------------------------------------------------------

        // traitement à excuter en troisième passage pour supprimer les téléphone trops courts
//        foreach ($adresses as $adresse) {
//            $telephone_domicile = str_replace(' ', '', $adresse->telephonedomicile);
//            $telephone_domicile = str_replace('+', '', $telephone_domicile);
//            $telephone_domicile = str_replace('/', '', $telephone_domicile);
//            $telephone_domicile = str_replace('.', '', $telephone_domicile);
//            $telephone_domicile = str_replace('-', '', $telephone_domicile);
//
//            if ($telephone_domicile != '') {
//                $int_telephone_domicile = intval($telephone_domicile);
//
//                if ($telephone_domicile != str_pad($int_telephone_domicile, strlen($telephone_domicile), '0', STR_PAD_LEFT)) {
//                    $data = array('telephonedomicile' => '');
//                    $adresse->update($data);
//                } else {
//                    $pays = strtolower($adresse->pays);
//                    if ($pays == 'france') {
//                        if (strlen($telephone_domicile) != 10 && strlen($telephone_domicile) != 9) {
//                            $data = array('telephonedomicile' => '');
//                            $adresse->update($data);
//                        }
//                    }
//                }
//            }
//
//
//            $telephone_mobile = str_replace(' ', '', $adresse->telephonemobile);
//            $telephone_mobile = str_replace('+', '', $telephone_mobile);
//            $telephone_mobile = str_replace('/', '', $telephone_mobile);
//            $telephone_mobile = str_replace('.', '', $telephone_mobile);
//            $telephone_mobile = str_replace('-', '', $telephone_mobile);
//            if ($telephone_mobile != '') {
//                $int_telephone_mobile = intval($telephone_mobile);
//                if ($telephone_mobile != str_pad($int_telephone_mobile, strlen($telephone_mobile), '0', STR_PAD_LEFT)) {
//                    $data = array('telephonemobile' => '');
//                    $adresse->update($data);
//                } else {
//                    $pays = strtolower($adresse->pays);
//                    if ($pays == 'france') {
//                        if (strlen($telephone_mobile) != 10 && strlen($telephone_mobile) != 9) {
//                            $data = array('telephonemobile' => '');
//                            $adresse->update($data);
//                        }
//                    }
//                }
//            }
//        }

//        -------------------------------------------------------------------------------------------------

        // traitement à excuter en troisième passage pour supprimer les téléphone trops courts
//        foreach ($adresses as $adresse) {
//            $telephone_domicile = str_replace(' ', '', trim($adresse->telephonedomicile));
//            if (str_starts_with($telephone_domicile, '6') || str_starts_with($telephone_domicile, '06') || str_starts_with($telephone_domicile, '7') || str_starts_with($telephone_domicile, '07')) {
//                if ($adresse->telephonemobile == '') {
//                    $data = array('telephonedomicile' => '', 'telephonemobile' => $telephone_domicile);
//                    $adresse->update($data);
//                } else {
//                    if ($telephone_domicile == str_replace(' ', '', trim($adresse->telephonemobile))) {
//                        $data = array('telephonedomicile' => '');
//                        $adresse->update($data);
//                    } else {
//                        $telephone_mobile = str_replace(' ', '', trim($adresse->telephonemobile));
//                        if (!str_starts_with($telephone_domicile, '6') && !str_starts_with($telephone_domicile, '06') && !str_starts_with($telephone_domicile, '7') && !str_starts_with($telephone_domicile, '07')) {
//                            $data = array('telephonedomicile' => $telephone_mobile, 'telephonemobile' => $telephone_domicile);
//                            $adresse->update($data);
//                        } else {
//                            $data = array('telephonedomicile' => '');
//                            $adresse->update($data);
//                        }
//                    }
//                }
//            }
//
//
//
//            $telephone_mobile = str_replace(' ', '', trim($adresse->telephonemobile));
//            if ($telephone_mobile != '') {
//                if (!str_starts_with($telephone_mobile, '6') && !str_starts_with($telephone_mobile, '06') && !str_starts_with($telephone_mobile, '7') && !str_starts_with($telephone_mobile, '07')) {
//                    if ($telephone_mobile == '0000000000') {
//                        $data = array('telephonemobile' => '');
//                        $adresse->update($data);
//                    } else {
//                        if ($adresse->telephonedomicile == '') {
//                            $data = array('telephonemobile' => '', 'telephonedomicile' => $telephone_mobile);
//                            $adresse->update($data);
//                        } else {
//                            $data = array('telephonemobile' => '');
//                            $adresse->update($data);
//                        }
//                    }
//                }
//            }
//
//        }

//        -------------------------------------------------------------------------------------------------

        // traitement à excuter en quatrièème passage
//        foreach ($adresses as $adresse) {
//            $telephone_mobile = str_replace(' ', '', $adresse->telephonemobile);
//            $telephone_mobile = str_replace('+', '', $telephone_mobile);
//            $telephone_mobile = str_replace('/', '', $telephone_mobile);
//            $telephone_mobile = str_replace('.', '', $telephone_mobile);
//            $telephone_mobile = str_replace('-', '', $telephone_mobile);
//            if ($telephone_mobile != $adresse->telephonemobile) {
//                $data = array('telephonemobile' => $telephone_mobile);
//                $adresse->update($data);
//            }
//
//            $telephone_domicile = str_replace(' ', '', $adresse->telephonedomicile);
//            $telephone_domicile = str_replace('+', '', $telephone_domicile);
//            $telephone_domicile = str_replace('/', '', $telephone_domicile);
//            $telephone_domicile = str_replace('.', '', $telephone_domicile);
//            $telephone_domicile = str_replace('-', '', $telephone_domicile);
//            if ($telephone_domicile != $adresse->telephonedomicile) {
//                $data = array('telephonedomicile' => $telephone_domicile);
//                $adresse->update($data);
//            }
//        }

//        -------------------------------------------------------------------------------------------------

        // traitement à excuter en cinsquième passage
//        foreach ($adresses as $adresse) {
//            $pays = strtolower($adresse->pays);
//            if ($pays == 'france') {
//                $telephone_mobile = $adresse->telephonemobile;
//                if (str_starts_with($telephone_mobile, '6') || str_starts_with($telephone_mobile, '7')) {
//                    $data = array('telephonemobile' => '+33.'.$telephone_mobile);
//                    $adresse->update($data);
//                }
//                if (str_starts_with($telephone_mobile, '06') || str_starts_with($telephone_mobile, '07')) {
//                    $data = array('telephonemobile' => '+33.'.substr($telephone_mobile, 1));
//                    $adresse->update($data);
//                }
//
//                $telephone_domicile = $adresse->telephonedomicile;
//                if (str_starts_with($telephone_domicile, '0')) {
//                    $data2 = array('telephonedomicile' => '+33.'.substr($telephone_domicile, 1));
//                    $adresse->update($data2);
//                }
//                if (str_starts_with($telephone_domicile, '1') || str_starts_with($telephone_domicile, '2') || str_starts_with($telephone_domicile, '3') || str_starts_with($telephone_domicile, '4')
//                || str_starts_with($telephone_domicile, '5') || str_starts_with($telephone_domicile, '8') || str_starts_with($telephone_domicile, '9')) {
//                    $data2 = array('telephonedomicile' => '+33.'.$telephone_domicile);
//                    $adresse->update($data2);
//                }
//            }
//        }
    }
}
