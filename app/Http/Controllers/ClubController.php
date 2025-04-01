<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\ClubTools;
use App\Concern\Hash;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Concern\VoteTools;
use App\Exports\InscritExport;
use App\Http\Requests\AdherentRequest;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubAbonnementRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Mail\SendEmailReinitPassword;
use App\Mail\SendModificationEmail;
use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Session;
use App\Models\Souscription;
use App\Models\Tarif;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ClubController extends Controller
{
    use Tools;
    use ClubTools;
    use Api;
    use Invoice;
    use Hash;
    use VoteTools;

    public function __construct()
    {
        $this->middleware(['checkLogin', 'clubAccess'])->except(['updateAdherent', 'removeAdherent', 'sendReinitLink']);
    }

    // affichage de la page d'accueil de gestion des clubs
    public function gestion()
    {
        $club = $this->getClub();
        $nb_sessions = Session::where('club_id', $club->id)->orderBy('start_date')->count();
//        $nb_sessions = Session::where('club_id', $club->id)->where('start_date', '>=', date('Y-m-d'))->orderBy('start_date')->count();
        return view('clubs.gestion', compact('club', 'nb_sessions'));
    }

    // affichage de la vue d'informations générales du club
    public function infosClub()
    {
        $club = $this->getClub();
        list($club, $activites, $equipements, $countries) = $this->getClubFormParameters($club);
        return view('clubs.infos_club', compact('club', 'activites', 'equipements', 'countries'));
    }

    // Mise a jour des infos générales du club (nom, logo, courriel, ..)
    public function updateGeneralite(ClubReunionRequest $request, Club $club)
    {
        $error = $this->updateClubGeneralite($club, $request);
        if ($error == 1) {
            return redirect()->back()->with('error', "L'image n'est pas au bon format. Veuillez télécharger une image au format .jpeg, .jpg ou .png");
        } elseif ($error == 2) {
            return redirect()->back()->with('error', "L'image est trop grande. Veuillez télécharger une image de taille maximum de 1 Mo ");
        }
        return redirect()->route('clubs.infos_club')->with('success', "Les informations générales du club ont été mises à jour");

    }

    // mise à jour de l'adresse du club
    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $code = $this->updateClubAdress($club, $request);
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
        $this->updateClubReunion($club, $request);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations de réunion du club ont été mises à jour");
    }


    // affichage de la vue de gestion des adhérents du club
    public function gestionAdherents($statut = null, $abonnement = null)
    {
        $club = $this->getClub();
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours, datedebutflorilege, datefinflorilege')->first();
        $numeroencours = $config->numeroencours;
        $florilege_actif = date('Y-m-d') >= $config->datedebutflorilege && date('Y-m-d') <= $config->datefinflorilege;
        $club->is_abonne = $club->numerofinabonnement >= $numeroencours;
        $club->numero_fin_reabonnement = $club->is_abonne ? $club->numerofinabonnement + 5 : $numeroencours + 5;
        $statut = $statut ?? "init";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->where('utilisateurs.adherent_club', 1)
            ->orderBy('personnes.nom')->orderBy('personnes.prenom')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0, 1, 2, 3, 4])) {
            $query = $query->where('utilisateurs.statut', $statut);
        } else {
            if ($statut == 'init') {
                $query = $query->whereIn('utilisateurs.statut', [0, 1, 2, 3]);
                $statut = 0;
            }
        }
        if (in_array($abonnement, [0, 1])) {
            $query = $query->where('personnes.is_abonne', $abonnement);
        }
        $adherents = $query->get();


        $reglement_en_cours = Reglement::where('statut', 0)->where('clubs_id', $club->id)->first();

        $abo_club = 0;
        $florilege_club = 0;
        $exist_reglement_en_cours = 0;
        if ($reglement_en_cours) {
//        if ($reglement_en_cours && $club->statut == 1) {
            if ($reglement_en_cours->aboClub == 1) {
                $abo_club = 1;
            }
            $florilege_club = $reglement_en_cours->florilegeClub;
            $exist_reglement_en_cours = 1;
        }

        $club->aboPreinscrit = $abo_club;
        $club->florilegePreinscrit = $florilege_club;
        $nb_florileges_club = Souscription::where('clubs_id', $club->id)->where('statut', 1)->sum('nbexemplaires');
        $club->nb_florileges = $nb_florileges_club;

        foreach ($adherents as $adherent) {
            $fin = '';
            if ($adherent->personne->is_abonne) {
                $personne_abonnement = Abonnement::where('personne_id', $adherent->personne_id)->where('etat', 1)->first();
                if ($personne_abonnement) {
                    $fin = $personne_abonnement->fin;
                }
            }
            $adherent->fin = $fin;
            $abo_adherent = 0;
            $florilege_adherent = 0;
            if ($reglement_en_cours) {
                $reglement_utilisateur = DB::table('reglementsutilisateurs')
                    ->where('reglements_id', $reglement_en_cours->id)
                    ->where('utilisateurs_id', $adherent->id_utilisateur)
                    ->first();
                if ($reglement_utilisateur) {
                    if ($reglement_utilisateur->abonnement == 1) {
                        $abo_adherent = 1;
                    }
                    if ($reglement_utilisateur->florilege > 0) {
                        $florilege_adherent = $reglement_utilisateur->florilege;
                    }
                }
                $adherent->aboPreinscrit = $abo_adherent;
                $adherent->florilegePreinscrit = $florilege_adherent;
            }

            // on regarde s'il y a une souscription en cours
            $nb_florileges = Souscription::where('personne_id', $adherent->personne_id)->where('statut', 1)->sum('nbexemplaires');
            $adherent->nb_florileges = $nb_florileges;
        }
        return view('clubs.adherents.index', compact('club', 'statut', 'abonnement', 'adherents', 'numeroencours',
            'exist_reglement_en_cours', 'florilege_actif'));
    }

    // affichage de la vue permettant la saisie pour la création d'un adhérent par responsable de club
    public function createAdherent()
    {
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
    public function storeAdherent(AdherentRequest $request)
    {
        $club = $this->getClub();

        $code = $this->storeClubAdherent($request, $club);
        if ($code == '0') {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user, "Ajout d'un adhérent " . trim(strtoupper($request->nom)) . " " . trim(strtoupper($request->prenom) . " au club " . $club->nom));
            }
            return redirect()->route('clubs.adherents.index')->with('success', "L'adhérent a bien  été ajouté");
        } else {
            return match ($code) {
                '1' => redirect()->back()->with('error', "Problème lors de la récupérartion des informations de la personne")->withInput(),
                '2' => redirect()->back()->with('error', "L'adresse email est invalide")->withInput(),
                '3 ' => redirect()->back()->with('error', "Le pays est invalide")->withInput(),
                '4' => redirect()->back()->with('error', "Téléphone mobile invalide")->withInput(),
                '5' => redirect()->back()->with('error', "Téléphone fixe invalide")->withInput(),
                default => redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de l'ajout de l'adhérent"),
            };
        }
    }

    public function storeExistingAdherent(Request $request) {
        $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.create')->with('error', "L'identifiant renseigné ne correspond à aucun adhérent existant");
        }
        $club = $this->getClub();
        if ($utilisateur->clubs_id == $club->id) {
            return redirect()->route('clubs.adherents.create')->with('error', "Cet adhérent fait déjà partie de votre club. Vous pouvez donc le renouveler");
        }
        $code = $this->storeExistingClubAdherent($utilisateur, $club);
        if ($code == '0') {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user, "Ajout d'un adhérent " . trim(strtoupper($utilisateur->personne->nom)) . " " . trim(strtoupper($utilisateur->personne->prenom) . " au club " . $club->nom));
            }
            return redirect()->route('clubs.adherents.index')->with('success', "L'adhérent a bien  été ajouté");
        } else {
            return redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de l'ajout de l'adhérent");
        }
    }

    // affichage de la vue des informations d'un adhérent du club
    public function editAdherent($utilisateur_id)
    {
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
    public function updateAdherent(AdherentRequest $request, $utilisateur_id)
    {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $code = $this->updateClubAdherent($request, $utilisateur);
        if ($code == '0') {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user, "Modification des informations de l'adhérent " . $utilisateur->nom);
            }
            return redirect()->route('clubs.adherents.index')->with('success', "Les informations de l'adhérent ont été mises à jour");
        } else {
            return match ($code) {
                '1' => redirect()->back()->with('error', "Une personne possédant cetta adresse email existe déjà dans la base de données")->withInput(),
                '2' => redirect()->back()->with('error', "L'adresse email est invalide")->withInput(),
                '3' => redirect()->back()->with('error', "Le pays est invalide")->withInput(),
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
        if ($this->updateFonctionClub($club->id, $fonction_id, $current_utilisateur_id, $request->adherent_id)) {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user, "Modification des fonctions du club");
            }
            return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été attribuée à un nouvel utilisateur");
        } else {
            return redirect()->route('clubs.fonctions.index')->with('error', "Cet utilisateur ne fait pas partie des adhérent du club");
        }
    }

    // ajout d'une fonction club
    public function addFonction(Request $request, $fonction_id)
    {
        //on vérifie que le nouvel utilisateur appartient au club
        $club = $this->getClub();

        if ($this->addFonctionClub($club->id, $fonction_id, $request->adherent_id)) {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user, "Ajout d'une fonction du club");
            }
            return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été ajoutée à cet utilisateur");
        } else {
            return redirect()->route('clubs.fonctions.index')->with('error', "Cet utilisateur ne fait pas partie des adhérent du club");
        }
    }

    // suppression d'une fonction Club
    public function deleteFonction($current_utilisateur_id, $fonction_id)
    {
        DB::table('fonctionsutilisateurs')->where("utilisateurs_id", $current_utilisateur_id)->where("fonctions_id", $fonction_id)->delete();
        if ($fonction_id == 97) {
            $this->removeAuthorCapabilities($current_utilisateur_id);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user, "Suppression d'une fonction du club");
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


    public function attentePaiementValidation()
    {
        $club = $this->getClub();
        return view('clubs.reglements.attente_paiement_validation', compact('club'));
    }

    public function attentePaiementValidationFlorilege()
    {
        $club = $this->getClub();
        return view('clubs.florilege.attente_paiement_validation', compact('club'));
    }

    public function validationPaiementCarte(Request $request)
    {
        $club = $this->getClub();
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            // on regarde si on doit traiter un règlement
            $reglement = Reglement::where('monext_token', $request->token)->where('statut', 0)->first();
            if ($reglement) {
                // on fait le traitement
                if ($this->saveReglement($reglement)) {
                    $data = array('statut' => 1, 'numerocheque' => 'Monext ' . $reglement->monext_token, 'dateenregistrement' => date('Y-m-d H:i:s'),
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
    public function florilege()
    {
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

    public function cancelPaiementFlorilege(Request $request)
    {
        $souscription = Souscription::where('monext_token', $request->token)->first();
        if ($souscription) {
            $souscription->delete();
        }
        return redirect()->route('clubs.florilege')->with('error', "Votre paiement a été annulé");
    }

    public function validationPaiementCarteFlorilege(Request $request)
    {
        $result = $this->getMonextResult($request->token);
        $souscription = Souscription::where('monext_token', $request->token)->first();
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            if ($souscription) {
                // on enregistre la validation de la souscription
                $data = ['statut' => 1, 'monext_token' => null, 'monext_link' => null, 'ref_reglement' => 'Monext ' . $souscription->monext_token];
                $souscription->update($data);

                if ($souscription->personne_id) {
                    $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                    $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'personne_id' => $souscription->personne_id];
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
    public function factures()
    {
        $club = $this->getClub();
        $invoices = \App\Models\Invoice::where('club_id', $club->id)->orderByDesc('created_at')->get();
        foreach ($invoices as $invoice) {
            list($tmp, $path) = explode('htdocs', $invoice->getStorageDir());
            $path .= '/' . $invoice->numero . '.pdf';
            $invoice->path = $path;
        }
        return view('clubs.factures.index', compact('club', 'invoices'));
    }

    public function statistiques() {
        $club = $this->getClub();
        $nb_adherents = Utilisateur::whereIn('statut', [2,3])->where('clubs_id', $club->id)->count();
        $nb_adherents_previous = DB::table('utilisateurs_prec')->whereIn('statut', [2,3])->where('clubs_id', $club->id)->count();
        $ratio_adherents = round(($nb_adherents - $nb_adherents_previous) * 100 / $nb_adherents_previous, 2);
        $nb_abonnements = Abonnement::join('personnes', 'personnes.id', '=', 'abonnements.personne_id')
            ->join('utilisateurs', 'utilisateurs.personne_id', '=', 'personnes.id')
            ->where('abonnements.etat', 1)
            ->where('utilisateurs.clubs_id', $club->id)
            ->count();
        $nb_souscriptions_indiv = Souscription::join('personnes', 'personnes.id', '=', 'souscriptions.personne_id')
            ->join('utilisateurs', 'utilisateurs.personne_id', '=', 'personnes.id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->where('souscriptions.statut', 1)
            ->sum('souscriptions.nbexemplaires');
        $nb_souscriptions_club = Souscription::where('clubs_id', $club->id)
            ->where('statut', 1)
            ->sum('nbexemplaires');
        $nb_souscriptions = $nb_souscriptions_indiv + $nb_souscriptions_club;

        // on récupère les classements régionaux et nationaux du club pour la saison en cours
        $classements_nationaux = DB::table('classementclubs')->join('competitions', 'competitions.id', '=', 'classementclubs.competitions_id')
            ->where('classementclubs.clubs_id', $club->id)
            ->where('competitions.saison', date('Y'))
            ->orderBy('competitions.id')
            ->selectRaw('classementclubs.place, classementclubs.total, competitions.nom')
            ->get();
        $classements_regionaux = DB::table('rclassementclubs')->join('rcompetitions', 'rcompetitions.id', '=', 'rclassementclubs.competitions_id')
            ->where('rclassementclubs.clubs_id', $club->id)
            ->where('rcompetitions.saison', date('Y'))
            ->orderBy('rcompetitions.id')
            ->selectRaw('rclassementclubs.place, rclassementclubs.total, rcompetitions.nom')
            ->get();
        return view('clubs.statistiques.index', compact('club', 'nb_adherents', 'nb_abonnements', 'nb_souscriptions',
            'ratio_adherents', 'nb_adherents_previous', 'classements_nationaux', 'classements_regionaux'));
    }

    public function statistiquesVotesPhases()
    {
        $club = $this->getClub();
        list($vote, $adherents) = $this->getNotVotedAdherents($club);
        return view('clubs.statistiques.vote_detail', compact('vote', 'club', 'adherents'));
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

    public function removeAdherent($utilisateur_id) {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return redirect()->back()->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $data = ['adherent_club' => 0];
        $utilisateur->update($data);
        return redirect()->back()->with('success', "L'adhérent a bien été enlevé de la liste visible du club. L'identifiant carte est conservé et son nom sera encore visible dans l'historique des concours.");
    }

    public function sendReinitLink($personne_id) {
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return redirect()->back()->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }

        $crypt = $this->encodeShortReinit();
        $personne->secure_code = $crypt;
        $personne->save();

        $link = env('APP_URL')."reinitPassword/" . $crypt;
        $mailSent = Mail::to($personne->email)->send(new SendEmailReinitPassword($link));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $this->registerAction($personne->id, 3, "Demande génération mot de passe");

        $mail = new \stdClass();
        $mail->titre = "Demande de réinitialisation de mot de passe";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;

        $this->registerMail($personne->id, $mail);
        return redirect()->back()->with('success', "Un mail a été envoyé à l'adhérent pour réinitialiser son mot de passe");
    }




    public function formations() {
        $club = $this->getClub();
        $sessions = Session::where('club_id', $club->id)->orderByDesc('start_date')->get();
//        $sessions = Session::where('club_id', $club->id)->where('start_date', '>=', date('Y-m-d'))->orderByDesc('start_date')->get();
        return view('clubs.formations.index', compact('sessions', 'club'));
    }

    public function inscrits(Session $session) {
        return view('clubs.formations.inscrits', compact('session'));
    }

    public function export(Session $session) {
        // on récupère tous les inscrits de la session
        $inscrits = $session->inscrits->where('attente', 0)->where('status', 1);
        foreach ($inscrits as $inscrit) {
            $utilisateur = Utilisateur::where('personne_id', $inscrit->personne_id)
                ->whereIn('statut', [0,1,2,3])
                ->orderByDesc('statut')
                ->selectRaw('identifiant')
                ->first();
            if ($utilisateur) {
                $inscrit->identifiant = $utilisateur->identifiant;
            }
        }
        $fichier = 'session_'.$session->id.'_inscrits_' . date('YmdHis') . '.xls';
        if (Excel::store(new InscritExport($inscrits), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            $texte = "Vous pouvez télécharger le fichier en cliquant sur le lien suivant : <a href='" . $file_to_download . "'>Télécharger</a>";
            return redirect()->route('clubs.sessions.inscrits', $session)->with('success', $texte);
        } else {
            return redirect()->route('clubs.sessions.inscrits', $session)->with('success', "Un problème est survenu lors de l'export");
        }
    }
}
