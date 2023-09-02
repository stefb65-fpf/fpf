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
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait Tools
{
    use Hash;

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
            $query = $query->where('numero', 'LIKE', '%' . $term . '%')->get();
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
        $numeroencours = $config->numeroencours;

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
                    $debut = $abonnement->fin + 1;
                    $fin = $abonnement->fin + 5;
                    $dataa = array('personne_id' => $utilisateur->personne_id, 'etat' => 0, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                } else {
                    // on crée un abonnement avec état 1
                    $debut = $numeroencours;
                    $fin = $numeroencours + 4;
                    $dataa = array('personne_id' => $utilisateur->personne_id, 'etat' => 1, 'debut' => $debut, 'fin' => $fin, 'reglement_id' => $reglement->id);
                }
                Abonnement::create($dataa);
            }
            $personne = Personne::where('id', $utilisateur->personne_id)->first();
            $personne->update($datap);
        }

        // on met à jour le club si besoin
        if ($reglement->aboClub == 1 || $reglement->adhClub == 1) {
            $club = Club::where('id', $reglement->clubs_id)->first();
            $datac = array('statut' => 2);
            if ($club->ct == 'N') {
                $datac['ct'] = '1';
                $datac['second_year'] = 1;
            }
            if ($club->second_year == 1) {
                $datac['second_year'] = 0;
            }
            if ($reglement->aboClub == 1) {
                if ($numeroencours > $club->numerofinabonnement) {
                    $datac['numerofinabonnement'] = $numeroencours + 5;
                } else {
                    $datac['numerofinabonnement'] = $club->numerofinabonnement + 5;
                }
            }
            $club->update($datac);
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
            $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'club_id' => $reglement->clubs_id];
            $this->createAndSendInvoice($datai);
        } else {
            if (str_starts_with($reglement->reference, 'ADH-REN-')) {
                $description = "Renouvellement adhésion individuelle";
            } else {
                $description = "Renouvellement abonnement seul";
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
            $ct = 2;
            if ($age < 18) {
                $tarif_id = 15;
                $tarif_id_supp = 23;
                $ct = 4;
            } else {
                if ($age < 25) {
                    $tarif_id = 14;
                    $tarif_id_supp = 23;
                    $ct = 3;
                }
            }
        } else {
            $tarif_id = 13;
            $tarif_id_supp = 0;
            $ct = 2;
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
                    'adresses_id' => $personne->adresses[0]->id,
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

    protected function setIdentifiant($codepostal)
    {
        $departement = substr($codepostal, 0, 2);
        $dpt = DB::table('departements')->where('numero', $departement)->first();
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
            $cartes_actives = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [1, 2, 3])->selectRaw('id, identifiant, urs_id, clubs_id')->get();
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

            $cartes_inactives = Utilisateur::where('personne_id', $personne->id)->whereIn('statut', [0, 4])->selectRaw('id, identifiant, urs_id, clubs_id')->get();
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
        $menu_admin = $personne->is_administratif;
        $menu_formation = !$personne->is_administratif;

        $cartes = [];
//        if (!$personne->is_administratif) {
        if (!$personne->is_administratif && $personne->is_adherent != 0) {
            // on regarde les functions sur chaque carte
            $utilisateurs = Utilisateur::where('personne_id', $personne->id)->orderBy('statut')->selectRaw('id, urs_id, clubs_id, identifiant, statut')->get();
            if (sizeof($utilisateurs) > 0) {
                $prec_statut3 = 4;
                foreach ($utilisateurs as $utilisateur) {
                    $fonctions = Fonction::join('fonctionsutilisateurs', 'fonctionsutilisateurs.fonctions_id', '=', 'fonctions.id')
                        ->where('fonctionsutilisateurs.utilisateurs_id', $utilisateur->id)
                        ->selectRaw('fonctions.id, fonctions.libelle, fonctions.instance')
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
                            $cartes[] = $utilisateur;
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
                        if (in_array($fonction->id, config('app.ur_functions'))) {
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
                    // TODO on contrôle les droits liés à l'utilisateur
                    if (sizeof($cartes[0]->droits) > 0) {
                        $menu_admin = true;
                    }
                }
            }
        }

        $menu = [
            'club' => $menu_club,
            'ur' => $menu_ur,
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

    protected function updateWpUser($email, $password)
    {
        try {
            DB::beginTransaction();
            $wp_user = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE user_email = '" . $email . "'");
            if (sizeof($wp_user) > 0) {
                $userId = $wp_user[0]->ID;
                $passwp = md5($password);
                DB::connection('mysqlwp')->statement("UPDATE wp_users SET user_pass = '" . $passwp . "' WHERE ID = $userId LIMIT 1");
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    protected function updateWpUserEmail($email, $new_email)
    {
        try {
            DB::beginTransaction();
            $wp_user = DB::connection('mysqlwp')->select("SELECT ID FROM wp_users WHERE user_email = '" . $email . "'");
            if (sizeof($wp_user) > 0) {
                $userId = $wp_user[0]->ID;
                DB::connection('mysqlwp')->statement("UPDATE wp_users SET user_email = '" . $new_email . "' WHERE ID = $userId LIMIT 1");
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
        //TODO: activer la ligne ci dessous et desactiver l'email par defaut
        $email = $user->email;
//        $email ="hellebore-contact@protonmail.com";
        //enregistrement de l'action de la personne
        //TODO: définir plus de types d'action
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
}
