<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Http\Requests\AdherentRequest;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubAbonnementRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    use Tools;
    use ClubTools;
    use Api;
    public function __construct()
    {
        $this->middleware(['checkLogin', 'clubAccess']);
    }

    // fonctions de gestion du club
    public function gestion()
    {
        $club = $this->getClub();
        return view('clubs.gestion', compact('club'));
    }

    public function infosClub()
    {
        $club = $this->getClub();
        list($club,$activites,$equipements,$countries) = $this->getClubFormParameters($club);
        return view('clubs.infos_club', compact('club', 'activites', 'equipements', 'countries'));
    }

    public function updateGeneralite( ClubReunionRequest $request, Club $club)
    {

        $this->updateClubGeneralite($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations générales du club ont été mises à jour");

    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $this->updateClubAdress($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club,$request);
        return redirect()->route('clubs.infos_club')->with('success', "Les informations de réunion du club ont été mises à jour");
    }


    // fonctions de gestion des adhérents du club
    public function gestionAdherents($statut = null,$abonnement = null)
    {
        $club = $this->getClub();
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        $club->is_abonne = $club->numerofinabonnement >= $numeroencours;
        $club->numero_fin_reabonnement = $club->is_abonne ? $club->numerofinabonnement + 5 : $numeroencours + 5;
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0,1,2,3, 4])) {
            $query = $query->where('utilisateurs.statut', $statut);
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

            // si la personne est abonnée, on récupère le numéro de fin de son abonnement
            //$adherent->fin = $adherent->personne->is_abonne ? $adherent->personne->abonnements->where('etat', 1)[1]['fin'] : '';
        }
        return view('clubs.adherents.index', compact('club','statut','abonnement','adherents'));
    }

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
        return view('clubs.adherents.create', compact('club', 'countries', 'utilisateur'));
    }

    public function storeAdherent(AdherentRequest $request) {
        $club = $this->getClub();
        if ($request->personne_id != null) {
            $personne = Personne::where('id', $request->personne_id)->first();
            if (!$personne) {
                return redirect()->route('clubs.adherents.create')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
            }
        } else {
            if (!filter_var(trim($request->email), FILTER_VALIDATE_EMAIL)) {
                return redirect()->route('clubs.adherents.create')->with('error', "L'adresse email n'est pas valide");
            }
            $pays = Pays::where('id', $request->pays)->first();
            if (!$pays) {
                return redirect()->route('clubs.adherents.create')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
            }
            $news = $request->news ? 1 : 0;
            $datap = array(
                'nom' => trim(strtoupper($request->nom)),
                'prenom' => trim($request->prenom),
                'sexe' => $request->sexe,
                'email' => trim($request->email),
                'password' => $this->generateRandomPassword(),
                'phone_mobile' => $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif),
                'datenaissance' => $request->datenaissance,
                'news' => $news,
                'is_adherent' => 1,
                'premiere_connexion'  => 1
            );
            $personne = Personne::create($datap);

            // on crée l'adresse
            $dataa = array(
                'libelle1' => $request->libelle1,
                'libelle2' => $request->libelle2,
                'codepostal' => $request->codepostal,
                'ville' => $request->ville,
                'pays' => $pays->nom,
                'telephonedomicile' => $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif)
            );
            $adresse = Adresse::create($dataa);

            // on lie l'adresse à la personne
            $personne->adresses()->attach($adresse->id);
        }
        // on cherche le max numeroutilisateur pour le club
        $max_numeroutilisateur = Utilisateur::where('clubs_id', $club->id)->max('numeroutilisateur');
        $numeroutilisateur = $max_numeroutilisateur + 1;
        $identifiant = str_pad($club->urs_id, 2, '0', STR_PAD_LEFT).'-'
            .str_pad($club->numero, 4, '0', STR_PAD_LEFT).'-'
            .str_pad($numeroutilisateur, 4, '0', STR_PAD_LEFT);

        // on calcule le ct par défaut avec la date de naissance
        // on calcule l'âge de la personne à partir de sa date de naissance
        $date_naissance = new \DateTime($request->datenaissance);
        $date_now = new \DateTime();
        $age = $date_now->diff($date_naissance)->y;
        $ct = 2;
        if ($age < 18) {
            $ct = 4;
        } else {
            if($age < 25) {
                $ct = 3;
            }
        }

        // on crée un nouvel utilisateur pour la personne dans le club
        $datau = array(
            'personne_id' => $personne->id,
            'urs_id' => $club->urs_id,
            'clubs_id' => $club->id,
            'adresses_id' => $personne->adresses[0]->id,
            'identifiant' => $identifiant,
            'numeroutilisateur' => $numeroutilisateur,
            'sexe' => $request->sexe,
            'nom' => trim(strtoupper($request->nom)),
            'prenom' => trim($request->prenom),
            'ct' => $ct,
            'statut' => 0
        );
        $utilisateur = Utilisateur::create($datau);
        return redirect()->route('clubs.adherents.index')->with('success', "L'adhérent a bien  été ajouté");
    }

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
        return view('clubs.adherents.edit', compact('club', 'utilisateur', 'countries'));
    }

    public function updateAdherent(AdherentRequest $request, $utilisateur_id) {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $pays = Pays::where('id', $request->pays)->first();
        if (!$pays) {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }

        // on récupère les infos personne à mettre à jour
        $personne = $utilisateur->personne;
        $datap = $request->only('nom', 'prenom', 'datenaissance', 'phone_mobile', 'sexe');
        $datap['phone_mobile'] = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
        $datap['news'] = $request->news ? 1 : 0;
        $personne->update($datap);

        // on récupère les infos adresse à mettre à jour
        $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville');
        $dataa['pays'] = $pays->nom;
        $dataa['telephonedomicile'] = $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif);
        $adresse = $personne->adresses[0];
        $adresse->update($dataa);

        // on récupère les infos adresse2 à mettre à jour
        if (sizeof($personne->adresses) > 1) {
            $dataa2 = [];
            $dataa2['libelle1'] = $request->adresse2_libelle1;
            $dataa2['libelle2'] = $request->adresse2_libelle2;
            $dataa2['codepostal'] = $request->adresse2_codepostal;
            $dataa2['ville'] = $request->adresse2_ville;
            $pays2 = Pays::where('id', $request->adresse2_pays)->first();
            $dataa2['telephonedomicile'] = $this->format_fixe_for_base($request->adresse2_telephonedomicile, $pays2->indicatif);
            $dataa2['pays'] = $pays2->nom;

            $adresse2 = $personne->adresses[1];
            $adresse2->update($dataa2);
        }

        return redirect()->route('clubs.adherents.index')->with('success', "Les informations de l'adhérent ont été mises à jour");

    }


    // fonction de gestion des fonctions du club
    public function gestionFonctions()
    {
        $club = $this->getClub();
        $adherents = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)
            ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
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
        return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été attribuée à un nouvel utilisateur");
    }

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
        return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été ajoutée à cet utilisateur");
    }

    public function deleteFonction($current_utilisateur_id, $fonction_id)
    {
        DB::table('fonctionsutilisateurs')->where("utilisateurs_id", $current_utilisateur_id)->where("fonctions_id", $fonction_id)->delete();
        return redirect()->route('clubs.fonctions.index')->with('success', "La fonction a été ôtée à cet utilisateur");
    }


    // fonction de gestion des règlements du club
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
                }
            }
            $code = 'ok';

        } else {
            $code = 'ko';
        }
        return view('clubs.reglements.validation_paiement_carte', compact('club', 'code'));
    }

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
