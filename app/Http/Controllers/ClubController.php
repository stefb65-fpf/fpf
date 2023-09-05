<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\ClubTools;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Http\Requests\AdherentRequest;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubAbonnementRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Mail\SendModificationEmail;
use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Souscription;
use App\Models\Tarif;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClubController extends Controller
{
    use Tools;
    use ClubTools;
    use Api;
    use Invoice;

    public function __construct()
    {
        $this->middleware(['checkLogin', 'clubAccess'])->except(['updateAdherent']);
    }

    // affichage de la page d'accueil de gestion des clubs
    public function gestion()
    {
        $club = $this->getClub();
        return view('clubs.gestion', compact('club'));
    }

    // affichage de la vue d'informations générales du club
    public function infosClub()
    {
        $club = $this->getClub();
        list($club,$activites,$equipements,$countries) = $this->getClubFormParameters($club);
        return view('clubs.infos_club', compact('club', 'activites', 'equipements', 'countries'));
    }

    // Mise a jour des infos générales du club (nom, logo, courriel, ..)
    public function updateGeneralite( ClubReunionRequest $request, Club $club)
    {
        $error = $this->updateClubGeneralite($club, $request);
        if($error == 1){
            return redirect()->back()->with('error', "L'image n'est pas au bon format. Veuillez télécharger une image au format .jpeg, .jpg ou .png");
        }elseif( $error == 2){
            return redirect()->back()->with('error', "L'image est trop grande. Veuillez télécharger une image de taille maximum de 1 Mo ");
        }
        return redirect()->route('clubs.infos_club')->with('success', "Les informations générales du club ont été mises à jour");

    }

    // mise à jour de l'adresse du club
    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $code = $this->updateClubAdress($club,$request);
        if ($code == 1) {
            return redirect()->route('clubs.infos_club')->with('error', "Le téléphone mobile est incorrect");
        }
        if ($code == 2) {
            return redirect()->route('clubs.infos_club')->with('error', "Le téléphone fixe est incorrect");
        }
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");
    }

    // mise à jour des informations de réunion du club
    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations de réunion du club ont été mises à jour");
    }


    // affichage de la vue de gestion des adhérents du club
    public function gestionAdherents($statut = null,$abonnement = null)
    {
        $club = $this->getClub();
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        $club->is_abonne = $club->numerofinabonnement >= $numeroencours;
        $club->numero_fin_reabonnement = $club->is_abonne ? $club->numerofinabonnement + 5 : $numeroencours + 5;
        $statut = $statut ?? "init";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0,1,2,3, 4])) {
            $query = $query->where('utilisateurs.statut', $statut);
        } else {
            if ($statut == 'init') {
                $query = $query->whereIn('utilisateurs.statut', [0,1,2,3]);
            }
        }
        if (in_array($abonnement, [0,1])) {
            $query = $query->where('personnes.is_abonne', $abonnement);
        }
        $adherents = $query->get();
        foreach ($adherents as $adherent) {
            $fin = '';
            if ($adherent->personne->is_abonne) {
                $personne_abonnement = Abonnement::where('personne_id', $adherent->personne_id)->where('etat', 1)->first();
                if ($personne_abonnement) {
                    $fin = $personne_abonnement->fin;
                }
            }
            $adherent->fin = $fin;
        }
        return view('clubs.adherents.index', compact('club','statut','abonnement','adherents'));
    }

    // affichage de la vue permettant la saisie pour la création d'un adhérent par responsable de club
    public function createAdherent() {
        $club = $this->getClub();
        $utilisateur = new Utilisateur();
        $personne = new Personne();
        $adresse = new Adresse();
        $adresse->pays = "france";
        $adresse->indicatif = "33";
        $utilisateur->personne = $personne;
        $utilisateur->personne->adresses = [$adresse];
        $countries = Pays::all();
        $prev = 'clubs';
        return view('clubs.adherents.create', compact('club', 'countries', 'utilisateur', 'prev'));
    }

    // enregistrement des informations pour la création d'un adhérent par responsable de club
    public function storeAdherent(AdherentRequest $request) {
        $club = $this->getClub();




//        if ($request->personne_id != null) {
//            $personne = Personne::where('id', $request->personne_id)->first();
//            if (!$personne) {
//                return redirect()->route('clubs.adherents.create')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
//            }
//        } else {
//            if (!filter_var(trim($request->email), FILTER_VALIDATE_EMAIL)) {
//                return redirect()->route('clubs.adherents.create')->with('error', "L'adresse email n'est pas valide");
//            }
//            $pays = Pays::where('id', $request->pays)->first();
//            if (!$pays) {
//                return redirect()->route('clubs.adherents.create')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
//            }
//            $news = $request->news ? 1 : 0;
//            $password = $this->generateRandomPassword();
//            $datap = array(
//                'nom' => trim(strtoupper($request->nom)),
//                'prenom' => trim($request->prenom),
//                'sexe' => $request->sexe,
//                'email' => trim($request->email),
//                'password' => $password,
//                'phone_mobile' => $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif),
//                'datenaissance' => $request->datenaissance,
//                'news' => $news,
//                'is_adherent' => 1,
//                'premiere_connexion'  => 1
//            );
//            $personne = Personne::create($datap);
//
//            $this->insertWpUser(trim($request->nom), trim($request->prenom), trim($request->email), $password);
//
//            // on crée l'adresse
//            $dataa = array(
//                'libelle1' => $request->libelle1,
//                'libelle2' => $request->libelle2,
//                'codepostal' => $request->codepostal,
//                'ville' => $request->ville,
//                'pays' => $pays->nom,
//                'telephonedomicile' => $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif)
//            );
//            $adresse = Adresse::create($dataa);
//
//            // on lie l'adresse à la personne
//            $personne->adresses()->attach($adresse->id);
//        }
//
//        // on cherche le max numeroutilisateur pour le club
//        $max_numeroutilisateur = Utilisateur::where('clubs_id', $club->id)->max('numeroutilisateur');
//        $numeroutilisateur = $max_numeroutilisateur + 1;
//        $identifiant = str_pad($club->urs_id, 2, '0', STR_PAD_LEFT).'-'
//            .str_pad($club->numero, 4, '0', STR_PAD_LEFT).'-'
//            .str_pad($numeroutilisateur, 4, '0', STR_PAD_LEFT);
//
//        // on calcule le ct par défaut avec la date de naissance
//        // on calcule l'âge de la personne à partir de sa date de naissance
//        $date_naissance = new \DateTime($request->datenaissance);
//        $date_now = new \DateTime();
//        $age = $date_now->diff($date_naissance)->y;
//        $ct = 2;
//        if ($age < 18) {
//            $ct = 4;
//        } else {
//            if($age < 25) {
//                $ct = 3;
//            }
//        }
//
//        // on crée un nouvel utilisateur pour la personne dans le club
//        $datau = array(
//            'personne_id' => $personne->id,
//            'urs_id' => $club->urs_id,
//            'clubs_id' => $club->id,
//            'adresses_id' => $personne->adresses[0]->id,
//            'identifiant' => $identifiant,
//            'numeroutilisateur' => $numeroutilisateur,
//            'sexe' => $request->sexe,
//            'nom' => trim(strtoupper($request->nom)),
//            'prenom' => trim($request->prenom),
//            'ct' => $ct,
//            'statut' => 0
//        );
//        Utilisateur::create($datau);


        $code = $this->storeClubAdherent($request, $club);
        if ($code == '0') {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user,"Ajout d'un adhérent ". trim(strtoupper($request->nom))." ".trim(strtoupper($request->prenom)." au club ".$club->nom));
            }
            return redirect()->route('clubs.adherents.index')->with('success', "L'adhérent a bien  été ajouté");
        } else {
            return match ($code) {
                '1' => redirect()->back()->with('error', "Problème lors de la récupérartion des informations de la personne")->withInput(),
                '2' => redirect()->back()->with('error', "L'adresse email est invalide")->withInput(),
                '3 '=> redirect()->back()->with('error', "Le pays est invalide")->withInput(),
                '4' => redirect()->back()->with('error', "Téléphone mobile invalide")->withInput(),
                '5' => redirect()->back()->with('error', "Téléphone fixe invalide")->withInput(),
                default => redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de l'ajout de l'adhérent"),
            };
        }
    }

    // affichage de la vue des informations d'un adhérent du club
    public function editAdherent($utilisateur_id) {
        $club = $this->getClub();
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if ($utilisateur->clubs_id != $club->id) {
            return redirect()->route('clubs.adherents.index')->with('error', "Cet utilisateur ne fait pas partie des adhérents du club");
        }
        $countries = Pays::all();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        if (!isset($utilisateur->personne->adresses[0])) {
            $new_adresse = new Adresse();
            $new_adresse->pays = 'France';
            $utilisateur->personne->adresses[] = $new_adresse;
        }
        $pays = Pays::where('nom', strtoupper(strtolower($utilisateur->personne->adresses[0]->pays)))->first();
        if ($pays) {
            $utilisateur->personne->adresses[0]->indicatif = $pays->indicatif;
        } else {
            $utilisateur->personne->adresses[0]->indicatif = "";
        }
        if (isset($utilisateur->personne->adresses[1])) {
            $pays = Pays::where('nom', strtoupper(strtolower($utilisateur->personne->adresses[1]->pays)))->first();
            if ($pays) {
                $utilisateur->personne->adresses[1]->indicatif = $pays->indicatif;
            } else {
                $utilisateur->personne->adresses[1]->indicatif = "";
            }
        }
        $prev = 'clubs';
        return view('clubs.adherents.edit', compact('club', 'utilisateur', 'countries', 'prev'));
    }

    // Mise à jour des informations d'un adhérent du club
    public function updateAdherent(AdherentRequest $request, $utilisateur_id) {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $code = $this->updateClubAdherent($request, $utilisateur);
        if ($code == '0') {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user,"Modification des informations de l'adhérent ". $utilisateur->nom);
            }
            return redirect()->route('clubs.adherents.index')->with('success', "Les informations de l'adhérent ont été mises à jour");
        } else {
            return match ($code) {
                '3 '=> redirect()->back()->with('error', "Le pays est invalide")->withInput(),
                '4' => redirect()->back()->with('error', "Téléphone mobile invalide")->withInput(),
                '5' => redirect()->back()->with('error', "Téléphone fixe invalide")->withInput(),
                default => redirect()->back()->with('error', "Un problème est survenu lors de la mise à jour des informations de l'adhérent")->withInput(),
            };
//            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la mise à jour des informations de l'adhérent");
        }
    }


    // affichage des fonctions du club
    public function gestionFonctions()
    {
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->orderBy('personnes.nom')
            ->orderBy('personnes.prenom')
            ->get();

        // on récupère les fonctions du club
        $fonctions = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->where('fonctions.instance', 3)
            ->selectRaw('fonctions.id, fonctions.libelle, utilisateurs.identifiant, personnes.nom, personnes.prenom , utilisateurs.id as id_utilisateur')
            ->orderBy('fonctions.ordre')
            ->get();
        $tab_fonctions = [];
        foreach ($fonctions as $fonction) {
            $tab_fonctions[$fonction->id] = $fonction;
        }
        return view('clubs.fonctions.index', compact('club', 'adherents', 'tab_fonctions'));
    }

    // mise à jour d'une fonction club
    public function updateFonction(Request $request, $current_utilisateur_id, $fonction_id)
    {
        //on vérifie que le nouvel utilisateur appartient au club
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id')
            ->get();
        $in_array = false;
        $new_utilisateur_id = $request->adherent_id;
        foreach ($adherents as $adherent) {
            if ($adherent->id == $new_utilisateur_id) {
                $in_array = true;
            }
        }
        if (!$in_array) {
            return redirect()->route('clubs.fonctions.index')->with('error', 'Cet utilisateur ne fait pas partie des adhérent du club');
        }
        //on ajoute la ligne correspondant à la table pivot
        $data_ap = array('utilisateurs_id' => $new_utilisateur_id, 'fonctions_id' => $fonction_id);
        DB::table('fonctionsutilisateurs')->insert($data_ap);
        //on supprime l'ancien utilisateur
        DB::table('fonctionsutilisateurs')->where("utilisateurs_id", $current_utilisateur_id)->where("fonctions_id", $fonction_id)->delete();

        $user = session()->get('user');
        if ($user) {
        $this->MailAndHistoricize($user,"Modification des fonctions du club");
        }

        return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été attribuée à un nouvel utilisateur");
    }

    // ajout d'une fonction club
    public function addFonction(Request $request, $fonction_id)
    {
        //on vérifie que le nouvel utilisateur appartient au club
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
            ->get();
        $in_array = false;
        $new_utilisateur_id = $request->adherent_id;
        foreach ($adherents as $adherent) {
            if ($adherent->id == $new_utilisateur_id) {
                $in_array = true;
            }
        }
        if (!$in_array) {
            return redirect()->route('clubs.fonctions.index')->with('error', 'Cet utilisateur ne fait pas partie des adhérent du club');
        }
        //on ajoute la ligne correspondant à la table pivot
        $data_ap = array('utilisateurs_id' => $new_utilisateur_id, 'fonctions_id' => $fonction_id);
        DB::table('fonctionsutilisateurs')->insert($data_ap);
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Ajout d'une fonction du club");
        }
        return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été ajoutée à cet utilisateur");
    }

    // suppression d'une fonction Club
    public function deleteFonction($current_utilisateur_id, $fonction_id)
    {
        DB::table('fonctionsutilisateurs')->where("utilisateurs_id", $current_utilisateur_id)->where("fonctions_id", $fonction_id)->delete();
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Suppression d'une fonction du club");
        }
        return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été ôtée à cet utilisateur");
    }


    // affichage des bordereaux de règlements du club
    public function gestionReglements()
    {
        $club = $this->getClub();
        // on récupère tous les règlements du club
        $reglements = Reglement::where('clubs_id', $club->id)->orderByDesc('id')->get();
        $dir = $club->getImageDir();
        list($tmp, $dir_club) = explode('htdocs/', $dir);
        $dir_club = env('APP_URL') . '/' . $dir_club;
        return view('clubs.reglements.index', compact('club', 'reglements', 'dir_club', 'dir'));
    }



    public function attentePaiementValidation() {
        $club = $this->getClub();
        return view('clubs.reglements.attente_paiement_validation', compact('club'));
    }

    public function attentePaiementValidationFlorilege() {
        $club = $this->getClub();
        return view('clubs.florilege.attente_paiement_validation', compact('club'));
    }

    public function validationPaiementCarte(Request $request) {
        $club = $this->getClub();
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on regarde si on doit traiter un règlement
            $reglement = Reglement::where('monext_token', $request->token)->where('statut', 0)->first();
            if ($reglement) {
                // on fait le traitement
                if ($this->saveReglement($reglement)) {
                    $data =array('statut' => 1, 'numerocheque' => 'Monext '.$reglement->monext_token, 'dateenregistrement' => date('Y-m-d H:i:s'),
                        'monext_token' => null, 'monext_link' => null);
                    $reglement->update($data);

                    $this->saveInvoiceForReglement($reglement);
                }
            }
            $code = 'ok';

        } else {
            $code = 'ko';
        }
        return view('clubs.reglements.validation_paiement_carte', compact('club', 'code'));
    }

    // affichage de la page de commande des Florilège pour le club
    public function florilege() {
        $club = $this->getClub();
//        $config = Configsaison::where('id', 1)->selectRaw('prixflorilegefrance, prixflorilegeetranger, datedebutflorilege, datefinflorilege')->first();
        $config = Configsaison::where('id', 1)->selectRaw('datedebutflorilege, datefinflorilege')->first();
        $tarif_florilege_france = Tarif::where('statut', 0)->where('id', 21)->first();
        $tarif_florilege_etranger = Tarif::where('statut', 0)->where('id', 22)->first();
        $config->prixflorilegefrance = $tarif_florilege_france->tarif;
        $config->prixflorilegeetranger = $tarif_florilege_etranger->tarif;
        if (!(date('Y-m-d') >= $config->datedebutflorilege && date('Y-m-d') <= $config->datefinflorilege)) {
            return redirect()->route('accueil');
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->first();
        if ($contact) {
            if (sizeof($contact->personne->adresses) > 1) {
                $adresse = $contact->personne->adresses[1];
            } else {
                $adresse = $contact->personne->adresses[0];
            }
        } else {
            $adresse = $club->adresse;
        }
        return view('clubs.florilege.index', compact('club', 'config', 'adresse', 'contact'));
    }

    public function cancelPaiementFlorilege(Request $request) {
        $souscription = Souscription::where('monext_token', $request->token)->first();
        if ($souscription) {
            $souscription->delete();
        }
        return redirect()->route('clubs.florilege')->with('error', "Votre paiement a été annulé");
    }

    public function validationPaiementCarteFlorilege(Request $request) {
        $result = $this->getMonextResult($request->token);
        $souscription = Souscription::where('monext_token', $request->token)->first();
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            if ($souscription) {
                // on enregistre la validation de la souscription
                $data = ['statut' => 1, 'monext_token' => null, 'monext_link' => null, 'ref_reglement' => 'Monext '.$souscription->monext_token];
                $souscription->update($data);

                if ($souscription->personne_id) {
                    $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                    $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'club_id' => $souscription->personne_id];
                    $this->createAndSendInvoice($datai);
                } else {
                    if ($souscription->clubs_id) {
                        $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                        $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'club_id' => $souscription->clubs_id];
                        $this->createAndSendInvoice($datai);
                    }
                }

                return redirect()->route('clubs.florilege')->with('success', "Votre paiement a été accepté et votre souscription a été enregistrée avec succès. Vous allez recevoir un mail récapitulatif de votre souscription.");
            }
            return redirect()->route('clubs.florilege')->with('error', "Votre paiement a été validé mais une erreur est survenue lors de l'enregistrement de votre souscription. Veuillez contacter le secrétariat");
        } else {
            if ($souscription) {
                $souscription->delete();
            }
            return redirect()->route('clubs.florilege')->with('error', "Votre paiement n'a pas été validé");
        }
    }

    // affichage des factures liées au club
    public function factures() {
        $club = $this->getClub();
        $invoices = \App\Models\Invoice::where('club_id', $club->id)->orderByDesc('created_at')->get();
        foreach ($invoices as $invoice) {
            list($tmp, $path) = explode('htdocs',  $invoice->getStorageDir());
            $path .= '/'.$invoice->numero.'.pdf';
            $invoice->path = $path;
        }
        return view('clubs.factures.index', compact('club', 'invoices'));
    }

    // fonction de récupération des infos club en fonction de l'identifiant de l'utilisateur
    protected function getClub()
    {
        $cartes = session()->get('cartes');
        if (!$cartes || count($cartes) == 0) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations club");
        }
        $active_carte = $cartes[0];
        $club_id = $active_carte->clubs_id;
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations club");
        }
        return $club;
    }
}
