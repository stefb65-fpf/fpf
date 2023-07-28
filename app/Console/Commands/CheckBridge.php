<?php

namespace App\Console\Commands;

use App\Concern\Api;
use App\Concern\Tools;
use App\Models\Personne;
use App\Models\Reglement;
use Illuminate\Console\Command;

class CheckBridge extends Command
{
    use Api;
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:bridge';

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
        $reglements = Reglement::where('statut', 0)->whereNotNull('bridge_id')->get();
        foreach($reglements as $reglement) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $reglement->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);

            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement a été effectué
                    if ($this->saveReglement($reglement)) {
                        $data =array('statut' => 1, 'bridge_id' => null, 'bridge_link' => null,
                            'numerocheque' => 'Bridge '.$reglement->bridge_id, 'dateenregistrement' => date('Y-m-d H:i:s'));
                        $reglement->update($data);
                    }
                }

                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    // le paiement n'a pas été effectué
                    $data =array('bridge_id' => null, 'bridge_link' => null);
                    $reglement->update($data);
                }
            }
        }
        dd($reglements);


        $personnes = Personne::where('attente_paiement', 1)->whereNotNull('bridge_id')->get();
        foreach ($personnes as $personne) {
            $url = 'https://api.bridgeapi.io/v2/payment-links/'. $personne->bridge_id;
            list($status, $reponse) = $this->callBridge($url, 'GET', null);

            if ($status == 200) {
                $tab_reponse = json_decode($reponse);
                dd($tab_reponse);
                if ($tab_reponse->status == 'REVOKED' || $tab_reponse->status == 'EXPIRED') {
                    // le paiement n'a pas été effectué
                    if ($personne->action_paiement == 'ADD_INDIVIDUEL') {
                        // on supprime la personne
                        $personne->delete();
                    }
                    $data =array('attend_paiement' => 0, 'bridge_id' => null, 'bridge_link' => null, 'action_paiement' => null);
                    $personne->update($data);
                }

                if ($tab_reponse->status == 'COMPLETED') {
                    // le paiement a été effectué
                    if ($personne->action_paiement == 'ADD_INDIVIDUEL') {
                        // on crée l'adhérent
                    }
                    $data =array('attend_paiement' => 0, 'bridge_id' => null, 'bridge_link' => null, 'action_paiement' => null);
                    $personne->update($data);
                }
            }
        }
    }
}
