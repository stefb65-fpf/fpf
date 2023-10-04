<?php

namespace App\Console\Commands;

use App\Concern\Tools;
use App\Models\Adresse;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessExcelSubscribe extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:subscribe';

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
//        try {
//            DB::beginTransaction();
//            $file = storage_path('app/public/users.csv');
//            $fp = fopen($file, "r");
//            $i = 0;
//            // on crée un règlement
//            $datar = [
//                'montant' => 0,
//                'numerocheque' => '',
//                'dateenregistrement' => date('Y-m-d H:i:s'),
//                'statut' => 01,
//                'clubs_id' => 554,
//                'reference' => '23-17-1288-0001'
//            ];
//            $reglement = Reglement::create($datar);
//            while (($data = fgetcsv($fp, 0, ";")) !== FALSE) {
//                if ($i > 0) {
//                    $identifiant = $data[0];
//                    $sexe = $data[1] == 'Mme' ? 1 : 0;
//                    $nom = strtoupper($data[2]);
//                    $prenom = $data[3];
//                    $adresse1 = $data[4];
//                    $adresse2 = $data[5];
//                    $code_postal = $data[6];
//                    $ville = $data[7];
//                    $email = $data[8];
//                    $mobile = str_starts_with($data[10], '6') || str_starts_with($data[10], '7') ? '+33'.$data[10] : '';
//                    $type_carte = $data[11];
//                    $ct = 2;
//                    if ($type_carte == '18-25 ans') {
//                        $ct = 3;
//                    }
//                    if ($type_carte == '< 18 ans') {
//                        $ct = 4;
//                    }
//                    if ($type_carte == 'Famille') {
//                        $ct = 5;
//                    }
//                    $abo = $data[12] == "Sans" ? 0 : 1;
//
//                    if ($identifiant == '') {
//                        $max_utilisateur = Utilisateur::where('identifiant', 'LIKE', '17-1288-%')->max('numeroutilisateur');
//                        $numero = $max_utilisateur ? $max_utilisateur + 1 : 1;
//                        $identifiant = '17-1288-'.str_pad($numero, 4, '0', STR_PAD_LEFT);
//                        // c'est une nouvelle personne
//                        // on vérifie que l'email n'exista pas en base
//                        $exist = Personne::where('email', $email)->first();
//                        if ($exist) {
//                            // c'est une nouvelle carte pour la personne existante
//                            $datau = [
//                                'urs_id' => 17,
//                                'clubs_id' => 554,
//                                'personne_id' => $exist->id,
//                                'identifiant' => $identifiant,
//                                'numeroutilisateur' => $numero,
//                                'sexe' => $exist->sexe,
//                                'nom' => $exist->nom,
//                                'prenom' => $exist->prenom,
//                                'ct' => $ct,
//                                'statut' => 1
//                            ];
//                            $utilisateur = Utilisateur::create($datau);
//                        } else {
//                            // on crée la personne et le user
//                            $password = $this->generateRandomPassword();
//                            $datap = array('sexe' => $sexe, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email,
//                                'phone_mobile' => $mobile, 'is_adherent' => 1, 'password' => $password);
//                            $personne = Personne::create($datap);
//
//                            $dataa = array('libelle1' => $adresse1, 'libelle2' => $adresse2, 'codepostal' => $code_postal,
//                                'ville' => $ville, 'pays' => 'France');
//                            $adresse = Adresse::create($dataa);
//
//                            $personne->adresses()->attach($adresse->id);
//
//                            $datau = [
//                                'urs_id' => 17,
//                                'clubs_id' => 554,
//                                'personne_id' => $personne->id,
//                                'identifiant' => $identifiant,
//                                'numeroutilisateur' => $numero,
//                                'sexe' => $personne->sexe,
//                                'nom' => $personne->nom,
//                                'prenom' => $personne->prenom,
//                                'ct' => $ct,
//                                'statut' => 1
//                            ];
//                            $utilisateur = Utilisateur::create($datau);
//                        }
//
//                    } else {
//                        // la carte existe déjà
//                        $datau = [
//                            'ct' => $ct,
//                            'statut' => 1
//                        ];
//                        $utilisateur = Utilisateur::where('identifiant', $identifiant)->first();
//                        $utilisateur->update($datau);
//                    }
//
//
//                    DB::table('reglementsutilisateurs')
//                        ->insert([
//                                'reglements_id' => $reglement->id,
//                                'utilisateurs_id' => $utilisateur->id,
//                                'adhesion' => 1,
//                                'abonnement' => $abo
//                            ]
//                        );
//                }
//                $i++;
//            }
//            fclose($fp);
//            DB::commit();
//        } catch (\Exception $e) {
//            dd($e);
//            DB::rollBack();
//        }
    }
}
