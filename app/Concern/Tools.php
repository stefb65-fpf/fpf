<?php


namespace App\Concern;


use App\Mail\SendAlertSupport;
use App\Mail\SendModificationEmail;
use App\Mail\SendSupportNotification;
use App\Mail\ValidationReglement;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Droit;
use App\Models\Fonction;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Souscription;
use App\Models\Tarif;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait Tools
{
    use Hash;
    use Invoice;

    /**
     * enregistre l'action utilisateur dans la table historique
     * @param $personne_id integer
     * @param $type integer (0: gestion profil, 1: abonnement adhésion, 2: formation, 3: connexion inscription
     * @param $action string
     * @param $utilisateur_id integer|null
     * @return bool
     */
    public function registerAction(int $personne_id, int $type, string $action, int $utilisateur_id = null)
    {
        if (!$personne_id || !$type || !$action) {
            return false;
        }
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return false;
        }
        $histo = Historique::create([
            'personne_id' => $personne_id,
            'utilisateur_id' => $utilisateur_id ?: null,
            'type' => $type,
            'action' => $action,
        ]);
        if (!$histo) {
            return false;
        }
        return true;
    }

    /**
     * @param null $user
     * @param $mail
     * @return bool
     */
    public function registerMail(int $personne_id, $mail, int $utilisateur_id = null)
    {
        if (!$personne_id || !$mail) {
            return false;
        }
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return false;
        }
        $histoMails = Historiquemail::create([
            'personne_id' => $personne_id,
            'utilisateur_id' => $utilisateur_id ?: null,
            'destinataire' => $mail->destinataire,
            'titre' => $mail->titre,
            'contenu' => $mail->contenu,
        ]);
        if (!$histoMails) {
            return false;
        }
        return true;
    }

    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function format_phone_number_visual($string)
    {
        $phone_number = $string;
        if (strlen($string)) {
            //si $string commence par un 0 et contient des espaces, on les enleve
            $phone_number = ltrim(str_replace(" ", "", $string), "0");
            //séparer avant et après le "."
            $tab = explode('.', $string);
            if (sizeof($tab) > 1) {
                $phone_number = $tab[1];
            }
//        ajouter des espaces
            $phone_number = chunk_split("0" . $phone_number, 2, ' ');
        }
        return $phone_number;
    }

    public function format_phone_number_callable($string)
    {
        $phone_number = $string;
        if (strlen($string)) {
            //si $string commence par un 0 et contient des espaces, on les enleve
            $phone_number = ltrim(str_replace(" ", "", $string), "0");
            //enlever le '.'
            $phone_number = str_replace('.', "", $string);
        }
        return $phone_number;
    }

    public function format_web_url($string)
    {
        $array = ['https://', 'www', 'http://'];
        if (strlen($string)) {
            $string = "https://" . ltrim(str_replace($array, "", $string), ".");
        }
        return $string;
    }

    public function format_fixe_for_base($number, $indicatif)
    {
        $false_number = false;
        if ($number) {
            $number = preg_replace('/[^0-9]/', '', $number);
            $number = ltrim($number, '0');
            if($indicatif == 33){
                if (!(strlen($number) == 9)) {
                    $false_number = true;
                }
            }
            $number = '+' . $indicatif . '.' . $number;
        }
        if($false_number){
            $number = -1;
        }
        return $number;
    }

    public function format_mobile_for_base($number, $indicatif = '33')
    {
        $false_number = false;
//        dd($number);
        if ($number) {
            $first_two_numbers = substr($number, 0, 2);
            // remove "0" and add "+33."
            $number= preg_replace('/[^0-9]/', '', $number);
            $number = ltrim($number, '0');
//            dd($number);
            if($indicatif == '33'){
//                dd("indicatif frznçais");
                if ($first_two_numbers == "06" || $first_two_numbers == "07") {
                    if (!(strlen($number) == 9)) {
                        $false_number = true;
                    }
                }else{
                    $false_number = true;
                }
            }
            $number = '+' . $indicatif . '.' . $number;
        }
        if($false_number){
            $number = -1;
        }
        return $number;
    }

    public function getClubsByTerm($term, $query)
    {
        if (is_numeric($term)) {
            $query = $query->where('numero', 'LIKE', '%' . trim($term, 0) . '%')->get();
        } else {
            $query = $query->where('nom', 'LIKE', '%' . $term . '%')->get();
        }
        return $query;
    }

    public function getPersonsByTerm($term, $query)
    {
        $query = $query->where(
            function ($query) use ($term) {
                $query->where('utilisateurs.identifiant', 'LIKE', '%' . $term . '%')
                    ->orWhere('personnes.email', 'LIKE', '%' . $term . '%')
                    ->orWhere('personnes.nom', 'LIKE', '%' . $term . '%')
                    ->orWhere('personnes.prenom', 'LIKE', '%' . $term . '%');
            }
        );
        return $query;
    }


    protected function getReglementsByTerm($term, $query)
    {
        if (is_numeric($term)) {
            $club = Club::where('numero', $term)->first();
            if ($club) {
                $query->where('clubs_id', $club->id);
            }
        } else {
            if (substr_count($term, '-') > 1) {
//            if ($this->isReference($term)) {
                $query->where('reference', $term);
            } else {
                $reglements_id = Utilisateur::join('reglementsutilisateurs', 'reglementsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                    ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                    ->where(
                        function ($query) use ($term) {
                            $query->where('personnes.nom', 'LIKE', '%' . $term . '%')
                                ->orWhere('personnes.prenom', 'LIKE', '%' . $term . '%');
                        }
                    )
                    ->selectRaw('reglementsutilisateurs.reglements_id')
                    ->get();
                $query->whereIn('id', $reglements_id); // à vérifier ici s'il faut transformer en array
            }
        }
        return $query;
    }

    protected function saveReglement($reglement)
    {
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
//        $config = Configsaison::where('id', 1)->selectRaw('numeroencours, prixflorilegefrance')->first();
        $numeroencours = $config->numeroencours;
//        $prix_florilege = $config->prixflorilegefrance;
        $tarif_florilege_france = Tarif::where('statut', 0)->where('id', 21)->first();
        $prix_florilege = $tarif_florilege_france->tarif;

        // on traite tous les utilisateurs en passant leur statut à 2 et / ou en prologeant ou créant leur abonnement
        $utilisateurs = Utilisateur::join('reglementsutilisateurs', 'utilisateurs.id', '=', 'reglementsutilisateurs.utilisateurs_id')
            ->where('reglementsutilisateurs.reglements_id', $reglement->id)
            ->get();

        foreach ($utilisateurs as $utilisateur) {
            $datap = array(); // données à mettre à jour sur la personne
            $datau = array(); // données à mettre à jour sur l'utilisateur
            if ($utilisateur->adhesion == 1) {
                $datau = array('statut' => 2, 'saison' => date('Y'));
                $datap['is_adherent'] = 1;
                $utilisateur->update($datau);
            }
            if ($utilisateur->abonnement == 1) {
                $datap['is_abonne'] = 1;

                // on regarde si l'utilisateur a déjà un abonnement en cours
                $abonnement = Abonnement::where('personne_id', $utilisateur->personne_id)->where('etat', 1)->first();
                if ($abonnement) {
                    // on crée un abonnement avec état 0
//                    $debut = $abonnement->fin + 1;
                    $fin = $abonnement->fin + 5;
                    $dataa = array('fin' => $fin);
                    $abonnement->update($dataa);
//                    $dataa = array('personne_id' => $utilisateur->personne_id, 'etat' => 0, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                } else {
                    // on crée un abonnement avec état 1
                    $debut = $numeroencours;
                    $fin = $numeroencours + 4;
                    $dataa = array('personne_id' => $utilisateur->personne_id, 'etat' => 1, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                    Abonnement::create($dataa);
                }
            }
            if ($utilisateur->florilege > 0) {
                // on insère des florilege dans la souscription
                $datas = array('personne_id' => $utilisateur->personne_id, 'reference' => $reglement->reference, 'nbexemplaires' => $utilisateur->florilege,
                    'montanttotal' => round($prix_florilege * $utilisateur->florilege, 2), 'statut' => 1,
                    'ref_reglement' => $reglement->reference);
                Souscription::create($datas);
            }
            $personne = Personne::where('id', $utilisateur->personne_id)->first();
            $personne->update($datap);
        }

        // on met à jour le club si besoin
        if ($reglement->aboClub == 1 || $reglement->adhClub == 1) {
            $club = Club::where('id', $reglement->clubs_id)->first();
            $datac = array('statut' => 2);
//            if ($club->ct == 'N') {
//                $datac['ct'] = '1';
//                $datac['second_year'] = 1;
//            }
//            if ($club->second_year == 1) {
//                $datac['second_year'] = 0;
//            }
            if ($reglement->aboClub == 1) {
                if ($numeroencours > $club->numerofinabonnement) {
                    $datac['numerofinabonnement'] = $numeroencours + 4;
                } else {
                    $datac['numerofinabonnement'] = $club->numerofinabonnement + 5;
                }
            }
            $club->update($datac);
        }

        if ($reglement->florilegeClub > 0) {
            // on insère des florilege dans la souscription
            $datas = array('clubs_id' => $reglement->clubs_id, 'reference' => $reglement->reference, 'nbexemplaires' => $reglement->florilegeClub,
                'montanttotal' => round($prix_florilege * $reglement->florilegeClub, 2), 'statut' => 1,
                'ref_reglement' => $reglement->reference);
            Souscription::create($datas);
        }

        if ($reglement->clubs_id) {
            // on récupère le contact du club et on luie envoie le mail de validation
            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $reglement->clubs_id)
                ->whereNotNull('utilisateurs.personne_id')
                ->first();
            if ($contact) {
                $this->sendMailValidationReglement($contact->personne, $reglement);
            }
        } else {
            // on envoie à tous les utilisateurs concernés par le règlement
            foreach ($utilisateurs as $utilisateur) {
                $this->sendMailValidationReglement($utilisateur->personne, $reglement);
            }
        }
        return true;
    }

    protected function saveInvoiceForReglement($reglement)
    {
        if ($reglement->clubs_id) {
            $description = "Renouvellement des adhésions et abonnements pour le club";
            $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'club_id' => $reglement->clubs_id, 'renew_club' => 1];
            $this->createAndSendInvoice($datai);
        } else {
            if (str_starts_with($reglement->reference, 'ADH-REN-')) {
                $description = "Renouvellement adhésion FPF référence " . $reglement->reference;
            } else {
                $description = "Renouvellement abonnement France Photographie référence ".$reglement->reference;
            }
            $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant];
            $utilisateurs = Utilisateur::join('reglementsutilisateurs', 'utilisateurs.id', '=', 'reglementsutilisateurs.utilisateurs_id')
                ->where('reglementsutilisateurs.reglements_id', $reglement->id)
                ->get();
            foreach ($utilisateurs as $utilisateur) {
                $datai['personne_id'] = $utilisateur->personne_id;
                $this->createAndSendInvoice($datai);
            }
        }
        return true;
    }

    protected function getCtForIndividuel($datenaissance) {
        $ct = 7;
        if ($datenaissance) {
            $date_naissance = new \DateTime($datenaissance);
            $date_now = new \DateTime();
            $age = $date_now->diff($date_naissance)->y;
            if ($age > 0) {
                if ($age < 18) {
                    $ct = 9;
                } else {
                    if ($age < 25) {
                        $ct = 8;
                    }
                }
            }
        }
        return $ct;
    }

    protected function getTarifByCt($ct)
    {
        $tarif_id = match ($ct) {
            '8' => 14,
            '9' => 15,
            'F' => 16,
            default => 13,
        };
        $tarif_id_supp = match ($ct) {
            '8', '9' => 23,
            default => 0,
        };
        $tarif_adhesion = Tarif::where('statut', 0)->where('id', $tarif_id)->first();
        $tarif = $tarif_adhesion ? $tarif_adhesion->tarif : 0;
        $tarif_abo = 0;
        if ($tarif_id_supp) {
            $tarif_supp = Tarif::where('statut', 0)->where('id', $tarif_id_supp)->first();
            $tarif_abo = $tarif_supp ? $tarif_supp->tarif : 0;
        }
        return [$tarif, $tarif_abo];
    }

    protected function getTarifAdhesion($datenaissance)
    {
        if ($datenaissance) {
            $date_naissance = new \DateTime($datenaissance);
            $date_now = new \DateTime();
            $age = $date_now->diff($date_naissance)->y;
            if ($age <= 0) {
                return [0, 0];
            }
            $tarif_id = 13;
            $tarif_id_supp = 0;
            $ct = 7;
            if ($age < 18) {
                $tarif_id = 15;
                $tarif_id_supp = 23;
                $ct = 9;
            } else {
                if ($age < 25) {
                    $tarif_id = 14;
                    $tarif_id_supp = 23;
                    $ct = 8;
                }
            }
        } else {
            $tarif_id = 13;
            $tarif_id_supp = 0;
            $ct = 7;
        }
        $tarif_adhesion = Tarif::where('statut', 0)->where('id', $tarif_id)->first();
        $tarif = $tarif_adhesion ? $tarif_adhesion->tarif : 0;
        $tarif_abo = 0;
        if ($tarif_id_supp) {
            $tarif_supp = Tarif::where('statut', 0)->where('id', $tarif_id_supp)->first();
            $tarif_abo = $tarif_supp ? $tarif_supp->tarif : 0;
        }
        return [$tarif, $tarif_abo, $ct];
    }

    protected function getTarifAbonnement($pays)
    {
        $tarif_id = $pays == 78 ? 19 : 20;
        $tarif = Tarif::where('statut', 0)->where('id', $tarif_id)->first();
        return $tarif ? $tarif->tarif : 0;
    }

    protected function saveNewPersonne($personne, $type)
    {
        try {
            DB::beginTransaction();
            $montant = 0;
            $ct = '';
            if ($personne->is_adherent == 1) {
                list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($personne->datenaissance);
                $montant = floatval($tarif);
                if ($personne->is_abonne == 1) {
                    $montant += floatval($tarif_supp);
                }
                $ref = 'ADH-NEW-' . $personne->id;
            } else {
                if ($personne->is_abonne == 1) {
                    $nom_pays = $personne->adresses()->first()->pays;
                    $pays = Pays::where('nom', $nom_pays)->first();
                    if ($pays) {
                        $montant = floatval($this->getTarifAbonnement($pays->id));
                    }
                }
                $ref = 'ABO-NEW-' . $personne->id;
            }
            // on crée le règlement avec la ref passée au paiement
            $numero_cheque = ($type == 'Bridge') ? 'Bridge ' . $personne->bridge_id : 'Monext ' . $personne->monext_token;
            $datar = [
                'montant' => $montant,
                'numerocheque' => $numero_cheque,
                'dateenregistrement' => date('Y-m-d H:i:s'),
                'statut' => 1,
                'reference' => $ref
            ];
            $reglement = Reglement::create($datar);

            // on regarde si c'est un adhérent
            if ($personne->is_adherent == 1) {
                // on crée un nouvel utilisateur
                list($identifiant, $urs_id, $numero) = $this->setIdentifiant($personne->adresses[0]->codepostal);
                $datau = [
                    'urs_id' => $urs_id,
                    'personne_id' => $personne->id,
                    'identifiant' => $identifiant,
                    'numeroutilisateur' => $numero,
                    'sexe' => $personne->sexe,
                    'nom' => $personne->nom,
                    'prenom' => $personne->prenom,
                    'ct' => $ct,
                    'statut' => 2,
                    'saison' => date('Y'),
                ];
                if ($personne->attente_premiere_carte) {
                    $datau['ct'] = 'F';
                    $datau['premierecarte'] = $personne->attente_premiere_carte;
                }
                $utilisateur = Utilisateur::create($datau);

                // on insère une ligne dans la table reglementsutilisateurs
                DB::table('reglementsutilisateurs')
                    ->insert([
                            'reglements_id' => $reglement->id,
                            'utilisateurs_id' => $utilisateur->id,
                            'adhesion' => $personne->is_adherent,
                            'abonnement' => $personne->is_abonne
                        ]
                    );
            }

            // on regarde si la personne est abonnée
            if ($personne->is_abonne) {
                // on crée un abonnement
                $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
                $dataa = ['personne_id' => $personne->id, 'etat' => 1, 'debut' => $numeroencours, 'fin' => $numeroencours + 4, 'reglement_id' => $reglement->id];
                Abonnement::create($dataa);
            }

            // on met à jour la personne
            $datap = ['attente_paiement' => 0, 'action_paiement' => null, 'monext_token' => null, 'monext_link' => null,
                'bridge_id' => null, 'bridge_link' => null, 'attente_premiere_carte' => null];
            $personne->update($datap);

            // on evoie le mail pour confirmer l'inscription ou l'abonnement
            $this->sendMailValidationReglement($personne, $reglement);
            $code = 'ok';

            DB::commit();
        } catch (\Exception $e) {
            $code = 'ko';
            $reglement = null;
            DB::rollBack();
        }
        return [$code, $reglement];
    }

    public function saveNewCard($personne, $type) {
        // on crée un règlement
        $numero_cheque = ($type == 'Bridge') ? 'Bridge ' . $personne->bridge_id : 'Monext ' . $personne->monext_token;
//        $tarif_adhesion = Tarif::where('statut', 0)->where('id', 13)->first();
        list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($personne->datenaissance);
        $exist_card = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [2,3])->first();
        if ($exist_card) {
            $tarif = $tarif / 2;
        }
        $add_abo = 0;
        if (!$exist_card && $ct == 7) {
            $add_abo = 1;
        }
        $montant = $tarif;
//        $montant = $tarif_adhesion->tarif;
        $ref = 'ADH-NEW-CARD-' . $personne->id;
        $last_reglement = Reglement::where('reference', 'LIKE', $ref.'%')->orderBy('id', 'DESC')->first();
        $num = $last_reglement ? intval(substr($last_reglement->reference, -4)) + 1 : 1;
        $ref .= '-'.str_pad($num, 4, '0', STR_PAD_LEFT);
        $datar = [
            'montant' => $montant,
            'numerocheque' => $numero_cheque,
            'dateenregistrement' => date('Y-m-d H:i:s'),
            'statut' => 1,
            'reference' => $ref
        ];
        $reglement = Reglement::create($datar);

        list($identifiant, $urs_id, $numero) = $this->setIdentifiant($personne->adresses[0]->codepostal);
        $datau = [
            'urs_id' => $urs_id,
            'personne_id' => $personne->id,
            'identifiant' => $identifiant,
            'numeroutilisateur' => $numero,
            'sexe' => $personne->sexe,
            'nom' => $personne->nom,
            'prenom' => $personne->prenom,
            'ct' => $ct,
//            'ct' => 2,
            'statut' => 2,
            'saison' => date('Y'),
        ];
        $utilisateur = Utilisateur::create($datau);

        // on insère une ligne dans la table reglementsutilisateurs
        DB::table('reglementsutilisateurs')
            ->insert([
                    'reglements_id' => $reglement->id,
                    'utilisateurs_id' => $utilisateur->id,
                    'adhesion' => 1,
                    'abonnement' => $add_abo
                ]
            );

        // on regarde s'il existe un abonnement en cours
        if ($add_abo == 1) {
            $abonnement = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $fin = $abonnement->fin + 5;
                $dataa = array('fin' => $fin);
                $abonnement->update($dataa);
            } else {
                // on crée un abonnement avec état 1
                $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
                $debut = $numeroencours;
                $fin = $numeroencours + 4;
                $dataa = array('personne_id' => $personne->id, 'etat' => 1, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                Abonnement::create($dataa);
            }
        }

        // on met à jour la personne
        $datap = ['attente_paiement' => 0, 'action_paiement' => null, 'monext_token' => null, 'monext_link' => null,
            'bridge_id' => null, 'bridge_link' => null, 'is_adherent' => 1];
        if ($add_abo == 1) {
            $datap['is_abonne'] = 1;
        }
        $personne->update($datap);
        $code = 'ok';

        return [$code, $reglement];

    }

    public function saveNewAbo($personne, $type) {
        $tarif_reduit = 0;
        $membre_club = 0;
        foreach ($personne->utilisateurs as $carte) {
            if ($carte->clubs_id) {
                $membre_club = 1;
                if (in_array($carte->statut, [2,3])) {
                    $tarif_reduit = 1;
                }
            }
        }
        if ($membre_club == 0) {
            $tarif_id = 19;
        } else {
            $tarif_id = $tarif_reduit ? 17 : 19;
        }
//        $tarif_id = $tarif_reduit ? 17 : 19;

        // on crée un règlement
        $numero_cheque = ($type == 'Bridge') ? 'Bridge ' . $personne->bridge_id : 'Monext ' . $personne->monext_token;
        $tarif_abonnement = Tarif::where('statut', 0)->where('id', $tarif_id)->first();
        $montant = $tarif_abonnement->tarif;
        $ref = 'ADH-NEW-ABO-'.$personne->id;
        $last_reglement = Reglement::where('reference', 'LIKE', $ref.'%')->orderBy('id', 'DESC')->first();
        $num = $last_reglement ? intval(substr($last_reglement->reference, -4)) + 1 : 1;
        $ref .= '-'.str_pad($num, 4, '0', STR_PAD_LEFT);
        $datar = [
            'montant' => $montant,
            'numerocheque' => $numero_cheque,
            'dateenregistrement' => date('Y-m-d H:i:s'),
            'statut' => 1,
            'reference' => $ref
        ];
        $reglement = Reglement::create($datar);

        // on insère une ligne dans la table reglementsutilisateurs
        if ($membre_club == 1) {
            $utilisateur = Utilisateur::where('id', $personne->utilisateurs[0]->id)->first();
            DB::table('reglementsutilisateurs')
                ->insert([
                        'reglements_id' => $reglement->id,
                        'utilisateurs_id' => $utilisateur->id,
                        'adhesion' => 0,
                        'abonnement' => 1
                    ]
                );
        }

        $abonnement = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
        if ($abonnement) {
            $fin = $abonnement->fin + 5;
            $dataa = array('fin' => $fin);
            $abonnement->update($dataa);
        } else {
            // on crée un abonnement avec état 1
            $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
            $debut = $numeroencours;
            $fin = $numeroencours + 4;
            $dataa = array('personne_id' => $personne->id, 'etat' => 1, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
            Abonnement::create($dataa);
        }

        // on met à jour la personne
        $datap = ['attente_paiement' => 0, 'action_paiement' => null, 'monext_token' => null, 'monext_link' => null,
            'bridge_id' => null, 'bridge_link' => null, 'is_abonne' => 1];
        $personne->update($datap);
        $code = 'ok';

        return [$code, $reglement];
    }

    protected function setIdentifiant($codepostal)
    {
        $codepostal = str_pad($codepostal, 5, '0', STR_PAD_LEFT);
        $departement = substr($codepostal, 0, 2);
        $dpt = DB::table('departements')->where('numero', $departement)->whereNotNull('urs_id')->first();
//        $dpt = DB::table('departementsurs')->where('numerodepartement', $departement)->first();
        if ($dpt) {
            $identifiant = str_pad($dpt->urs_id, 2, '0', STR_PAD_LEFT);
            $urs_id = $dpt->urs_id;
        } else {
            $identifiant = '99';
            $urs_id = 99;
        }
        $identifiant .= '-0000-';
        $max_utilisateur = Utilisateur::where('identifiant', 'LIKE', $identifiant . '%')->max('numeroutilisateur');
        $numero = $max_utilisateur ? $max_utilisateur + 1 : 1;
        $identifiant .= str_pad($numero, 4, '0', STR_PAD_LEFT);
        return array($identifiant, $urs_id, $numero);
    }

    protected function getUrFromCodepostal($codepostal) {
        $codepostal = str_pad($codepostal, 5, '0', STR_PAD_LEFT);
        $departement = substr($codepostal, 0, 2);
        $dpt = DB::table('departements')->where('numero', $departement)->whereNotNull('urs_id')->first();
        if ($dpt) {
            $ur = Ur::where('id', $dpt->urs_id)->first();
        } else {
            $ur = null;
        }
        return $ur;
    }

    protected function sendMailValidationReglement($personne, $reglement)
    {
        $email = $personne->email;
        $mailSent = Mail::to($email)->send(new ValidationReglement($reglement));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $mail = new \stdClass();
        $mail->titre = "Validation de votre règlement FPF";
        $mail->destinataire = $email;
        $mail->contenu = $htmlContent;
        $this->registerMail($personne->id, $mail);
        return true;
    }

    public function generateRandomPassword()
    {
        $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*";
        $shuffle_letters = str_shuffle($letters);
        $random_password = substr($shuffle_letters, 0, 8);
        return $this->encodePwd($random_password);
    }

    protected function getSituation($personne)
    {
        if ($personne->is_adherent) {
            // on recherche les cartes actives
            $tab_cartes = [];
            $cartes_actives = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [1, 2, 3])->selectRaw('id, identifiant, urs_id, clubs_id, statut, ct')->get();
            foreach ($cartes_actives as $carte) {
                $carte->actif = true;
                // on cherche les focntions de la carte
                $fonctions = Fonction::join('fonctionsutilisateurs', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
                    ->select('fonctions.id', 'fonctions.libelle')
                    ->where('fonctionsutilisateurs.utilisateurs_id', $carte->id)->get();
                $droits = [];
                $tab_fonctions = [];
                foreach ($fonctions as $fonction) {
                    if ($fonction->droits) {
                        foreach ($fonction->droits as $droit) {
                            $droits[] = $droit->label;
                        }
                    }
                    $tab_fonctions[] = array('id' => $fonction->id, 'libelle' => $fonction->libelle);
                }
                $carte->fonctions = $tab_fonctions;

                foreach ($carte->droits as $droit) {
                    $droits[] = $droit->label;
                }

                $carte->droits = $droits;
                $tab_cartes[] = $carte;
            }

            $cartes_inactives = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [0, 4])->selectRaw('id, identifiant, urs_id, clubs_id, statut, ct')->get();
            foreach ($cartes_inactives as $carte) {
                $carte->actif = false;
                $tab_cartes[] = $carte;
            }
            $personne->cartes = $tab_cartes;
        }

        if ($personne->is_abonne == 1) {
            // on recherche son abonnement en cours
            $abonnement = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $personne->abonnement = $abonnement;
            }
        }

        if ($personne->is_formateur) {
            // on recherche les infos formateur
        }

        return $personne;
    }

    protected function getMenu($personne)
    {
        // on doit déterminer les accès de l'utilisateur et les pousser dans la session
        $menu_club = false;
        $menu_ur = false;
        $menu_ur_general = false;
        $menu_admin = $personne->is_administratif;
        $menu_formation = !$personne->is_administratif && $personne->is_adherent;

        $cartes = [];
//        if (!$personne->is_administratif) {
        if (!$personne->is_administratif && $personne->is_adherent != 0) {
            // on regarde les functions sur chaque carte
            $utilisateurs = Utilisateur::where('personne_id', $personne->id)->where('statut', '<', 10)->orderBy('statut')->selectRaw('id, urs_id, clubs_id, identifiant, statut, saison, ct')->get();
            if (sizeof($utilisateurs) > 0) {
                $prec_statut3 = 4;
                foreach ($utilisateurs as $utilisateur) {
                    $fonctions = Fonction::join('fonctionsutilisateurs', 'fonctionsutilisateurs.fonctions_id', '=', 'fonctions.id')
                        ->where('fonctionsutilisateurs.utilisateurs_id', $utilisateur->id)
                        ->selectRaw('fonctions.id, fonctions.libelle, fonctions.instance, fonctions.parent_id')
                        ->orderBy('fonctions.instance')
                        ->orderBy('fonctions.ordre')
                        ->get();
                    $utilisateur->fonctions = $fonctions;
                    if ($utilisateur->statut == 3) {
                        if (sizeof($fonctions) > 0) {
                            if ($fonctions[0]->instance < $prec_statut3) {
                                array_unshift($cartes, $utilisateur);
                            } else {
                                $cartes[] = $utilisateur;
                            }
                            $prec_statut3 = $fonctions[0]->instance;
                        } else {
                            if (isset($cartes[0]) && !in_array($cartes[0]->statut, [2,3])) {
                                array_unshift($cartes, $utilisateur);
                            } else {
                                $cartes[] = $utilisateur;
                            }
                        }
                    } else {
                        $cartes[] = $utilisateur;
                    }
                }
                if (sizeof($cartes[0]->fonctions) > 0) {
                    foreach ($cartes[0]->fonctions as $fonction) {
                        if (in_array($fonction->id, config('app.club_functions'))) {
                            $menu_club = true;
                        }
                        if (in_array($fonction->id, config('app.ur_functions')) || in_array($fonction->parent_id, config('app.ur_functions'))) {
                            $menu_ur = true;
                        }


                        if ($fonction->instance == 1) {
                            // on contrôle les droits liés à la fonction
                            if (sizeof($fonction->droits)) {
                                $menu_admin = true;
                            }
                        }
                    }
                }

                if (!$menu_admin) {
                    if (sizeof($cartes[0]->droits) > 0) {
                        foreach ($cartes[0]->droits as $droit) {
                            if (!in_array($droit->label, ['GESNEWUR', 'GESNEWURCA'])) {
                                $menu_admin = true;
                            }
                        }
                    }
//                    if (sizeof($cartes[0]->droits) > 0) {
//                        $menu_admin = true;
//                    }
                }
                $menu_ur_general = $menu_ur;
                if (!$menu_ur) {
                    if (sizeof($cartes[0]->droits) > 0) {
                        foreach ($cartes[0]->droits as $droit) {
                            if (in_array($droit->label, ['GESNEWUR', 'GESNEWURCA', 'GESVOTUR'])) {
                                $menu_ur = true;
                            }
                        }
                    }
                    if (sizeof($cartes[0]->fonctions) > 0) {
                        foreach ($cartes[0]->fonctions as $fonction) {
                            if ($fonction->instance == 2) {
                                foreach ($fonction->droits as $droit) {
                                    if (in_array($droit->label, ['GESNEWUR', 'GESNEWURCA', 'GESVOTUR'])) {
                                        $menu_ur = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $menu = [
            'club' => $menu_club,
            'ur' => $menu_ur,
            'ur_general' => $menu_ur_general,
            'admin' => $menu_admin,
            'formation' => $menu_formation,
        ];

        return [$menu, $cartes];
    }

    protected function insertWpUser($firstname, $lastname, $email, $password)
    {
        try {
            DB::beginTransaction();
            $max_user_before = DB::connection('mysqlwp')->select("SELECT MAX(ID) as max FROM wp_users");
            $max_before = $max_user_before[0]->max;

            $firstname_wp = ucfirst(strtolower(addslashes($firstname)));
            $lastname_wp = ucfirst(strtolower(addslashes($lastname)));
            $display = $firstname_wp . ' ' . $lastname_wp;
            $identifiant = strtolower($firstname_wp . '.' . $lastname_wp . '.' . uniqid());
            $now = date('Y-m-d H:i:s');
            $passwp = md5($password);

            DB::connection('mysqlwp')->statement("INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status,
                display_name) VALUES ('" . $identifiant . "', '" . $passwp . "', '" . $identifiant . "', '" . $email . "', '', '" . $now . "', '', 0, '" . $display . "')");

            $max_user_after = DB::connection('mysqlwp')->select("SELECT MAX(ID) as max FROM wp_users");
            $max_after = $max_user_after[0]->max;
            if ($max_after != $max_before) {
                // on insère les user meta
                $metas = array(
                    'nickname' => $identifiant,
                    'first_name' => $firstname_wp,
                    'last_name' => $lastname_wp,
                    'description' => '',
                    'rich_editing' => 'true',
                    'comment_shortcuts' => 'false',
                    'admin_color' => 'fresh',
                    'use_ssl' => '0',
                    'wp_user_level' => '0',
                    'locale' => '',
                    'wp_capabilities' => 'a:2:{s:8:"adhrents";b:1;s:15:"bbp_participant";b:1;}',
                    'show_admin_bar_front' => 'false'
                );
                foreach ($metas as $key => $meta) {
                    $statement = "INSERT INTO wp_usermeta (user_id, meta_key, meta_value) VALUES ('" . $max_after . "', '" . $key . "', '" . $meta . "')";
                    DB::connection('mysqlwp')->statement($statement);
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    protected function deleteWpUser($email)
    {
        try {
            DB::beginTransaction();
            $wp_user = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE user_email = '" . $email . "'");
            if (sizeof($wp_user) > 0) {
                $userId = $wp_user[0]->ID;

                DB::connection('mysqlwp')->statement("DELETE FROM wp_users WHERE ID = $userId LIMIT 1");
                DB::connection('mysqlwp')->statement("DELETE FROM wp_usermeta WHERE user_id = $userId");
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    protected function updateWpUser($personne, $password)
    {
        try {
            DB::beginTransaction();
            $wp_user = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE user_email = '" . $personne->email . "'");
            if (sizeof($wp_user) > 0) {
                $userId = $wp_user[0]->ID;
                $passwp = md5($password);
                DB::connection('mysqlwp')->statement("UPDATE wp_users SET user_pass = '" . $passwp . "' WHERE ID = $userId LIMIT 1");
            } else {
                $this->insertWpUser($personne->prenom, $personne->nom, $personne->email, $password);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    protected function updateWpUserEmail($email, $personne)
    {
        try {
            DB::beginTransaction();
            $wp_user = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE user_email = '" . $email . "'");
            if (sizeof($wp_user) > 0) {
                $userId = $wp_user[0]->ID;
                DB::connection('mysqlwp')->statement("UPDATE wp_users SET user_email = '" . $personne->email . "' WHERE ID = $userId LIMIT 1");
            } else {
                $this->insertWpUser($personne->prenom, $personne->nom, $personne->email, $personne->password);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    protected function getUserFromWp($pass, $id)
    {
        $wp_user = DB::connection('mysqlwp')->select("SELECT user_email FROM wp_users WHERE user_pass = '" . $pass . "' AND id = $id LIMIT 1");
        if (sizeof($wp_user) > 0) {
            $email_user = $wp_user[0]->user_email;
            if ($email_user != '') {
//                $personne = Personne::where('email', 'deuxcartes@test.fr')->first();
                $personne = Personne::where('email', $email_user)->first();
                if ($personne) {
                    return $personne;
                }
            }
        }
        return null;
    }

    protected function MailAndHistoricize($user, $object)
    {
        $email = $user->email;
        //enregistrement de l'action de la personne
        $this->registerAction($user->id, 4, $object);
        // enregistrement du mail de la personne
        $mailSent = Mail::to($email)->send(new SendModificationEmail($object));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = $object;
        $mail->destinataire = $email;
        $mail->contenu = $htmlContent;
        $this->registerMail($user->id, $mail);
    }

    protected function sendMailSupport($support) {
        // on récupère tous les mails de l'équipe support
        $droit = Droit::where('label', 'SUPPORT')->first();
        Mail::to('contact@episteme-web.com')->send(new SendAlertSupport($support));
        foreach($droit->fonctions as $fonction) {
            $utilisateurs = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
                ->get();
            foreach($utilisateurs as $utilisateur) {
                $email = $utilisateur->personne->email;
                Mail::to($email)->send(new SendAlertSupport($support));
            }
        }
        if ($droit) {
            foreach ($droit->utilisateurs as $utilisateur) {
                $email = $utilisateur->personne->email;
                Mail::to($email)->send(new SendAlertSupport($support));
            }
        }
    }


    protected function checkDroit($strdroit)
    {
        $cartes = session()->get('cartes');
        $user = session()->get('user');
        if ($user->is_administratif) {
            return true;
        }
        if (!isset($cartes[0])) {
            return false;
        }
        $droits = [];
        foreach($cartes[0]->droits as $droit) {
            $droits[] = $droit->label;
        }
        foreach ($cartes[0]->fonctions as $fonction) {
            foreach ($fonction->droits as $droit) {
                $droits[] = $droit->label;
            }
        }
        if (!in_array($strdroit, $droits)) {
            return false;
        }
        return true;
    }

    protected function addAuthorCapabilities($utilisateur_id) {
        // si on n'est pas en production, on sort
        if (env('APP_ENV') != 'production') {
            return false;
        }
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return false;
        }
        $wp_usermeta = DB::connection('mysqlwp')->select("SELECT U.ID, M.meta_value FROM wp_users U, wp_usermeta M WHERE U.ID = M.user_id AND M.meta_key = 'wp_capabilities' AND U.user_email = '" . $utilisateur->personne->email . "' ORDER BY ID DESC LIMIT 1");
        if (sizeof($wp_usermeta) > 0) {
            $wp_capabilities = unserialize($wp_usermeta[0]->meta_value);
            if (!isset($wp_capabilities['ptb_clubsur_author'])) {
                $wp_capabilities['ptb_clubsur_author'] = true;
            }
            DB::connection('mysqlwp')->statement("UPDATE wp_usermeta SET meta_value = '" . serialize($wp_capabilities) . "' WHERE user_id = " . $wp_usermeta[0]->ID . " AND meta_key = 'wp_capabilities'");
        }
        return true;
    }

    protected function removeAuthorCapabilities($utilisateur_id) {
        if (env('APP_ENV') != 'production') {
            return false;
        }
        // on récupère l'utilisateur et on contrôle s'il a encore une fonction club ou UR
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return false;
        }

        $exist_fonction = DB::table('fonctionsutilisateurs')
            ->where('utilisateurs_id', $utilisateur_id)
            ->whereIn('fonctions_id', [57, 58, 97, 87, 336])
            ->first();
        if (!$exist_fonction) {
            // si pas de fonction, on récupère les capabilities dans la table wp_usermeta
            $wp_usermeta = DB::connection('mysqlwp')->select("SELECT U.ID, M.meta_value FROM wp_users U, wp_usermeta M WHERE U.ID = M.user_id AND M.meta_key = 'wp_capabilities' AND U.user_email = '" . $utilisateur->personne->email . "' ORDER BY ID DESC LIMIT 1");
            if (sizeof($wp_usermeta) > 0) {
                $wp_capabilities = unserialize($wp_usermeta[0]->meta_value);
                unset($wp_capabilities['ptb_clubsur_author']);
                DB::connection('mysqlwp')->statement("UPDATE wp_usermeta SET meta_value = '" . serialize($wp_capabilities) . "' WHERE user_id = " . $wp_usermeta[0]->ID . " AND meta_key = 'wp_capabilities'");
            }
        }
        return true;
    }


}
