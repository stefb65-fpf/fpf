<?php

namespace App\Http\Controllers;

use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Concern\UrTools;
use App\Http\Requests\AdherentRequest;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Http\Requests\PersonneRequest;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Fonction;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UrController extends Controller
{
    use Tools;
    use ClubTools;
    use UrTools;

    public function __construct()
    {
        $this->middleware(['checkLogin', 'urAccess']);
    }

    // affichage de la page d'accueil pour les URS
    public function gestion()
    {
        $ur = $this->getUr();
        return view('urs.gestion', compact('ur'));
    }

    // affichage de la liste des adhérents d'une UR
    public function list($view_type, $statut = null, $type_carte = null, $type_adherent = null, $term = null)
    {
        $statut = $statut ?? "all";
        $type_adherent = $type_adherent ?? "all";
        $ur = $this->getUr();
        $query = Utilisateur::where('urs_id', '=', $ur->id)->where('personne_id', '!=', null);
        if ($view_type == "recherche" && $term) {
            $query = Personne::join('utilisateurs', 'utilisateurs.personne_id', '=', 'personnes.id')
                ->where('utilisateurs.urs_id', '=', $ur->id);
            $this->getPersonsByTerm($term, $query);
        }

        if ($statut != 'all' && in_array($statut, [0, 1, 2, 3, 4])) {
            $query = $query->where('statut', $statut);
        }
        if ($type_adherent != 'all' && in_array($type_adherent, [1, 2])) {
            if ($type_adherent == 1) {
                $query = $query->whereNull('clubs_id');
            } else {
                $query = $query->whereNotNull('clubs_id');
            }
        }
        if ($type_carte != 'all' && (in_array($type_carte, [2, 3, 4, 5, 6, 7, 8, 9, "F"]))) {
            $query = $query->where('ct', $type_carte);
        }
        $utilisateurs = $query->paginate(100);
        $urs = Ur::orderBy('nom')->get();
        $ur_id = $ur->id;
        $level = 'urs';
        return view('admin.personnes.liste', compact('view_type', 'utilisateurs', 'level', 'statut', 'type_carte', 'type_adherent', 'ur_id', 'urs', 'ur', 'term'));
    }

    // affichage des informations d'un adhérent de l'UR
    public function editPersonne($personne_id, $view_type = null) {
        $ur = $this->getUr();
        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return redirect('/urs/personnes/'.$view_type)->with('error', "Un problème est survenu lors de la récupération des informations de la personne");
        }
        $trouve = false;
        foreach ($personne->utilisateurs as $utilisateur) {
            if ($utilisateur->urs_id == $ur->id) {
                $trouve = true;
            }
        }
        if (!$trouve) {
            return redirect('/urs/personnes/'.$view_type)->with('error', "Vous ne pouvez pas accéder aux information de cette personne ne possédant pas de cartes dans cette UR");
        }
        if (sizeof($personne->adresses) == 0) {
            return redirect('/urs/personnes/'.$view_type)->with('error', "Un problème est survenu lors de la récupération des adresses de la personne");
        }
        foreach ($personne->adresses as $adresse) {
            $pays = Pays::where('nom', $adresse->pays)->first();
            if ($pays) {
                $adresse->indicatif = $pays->indicatif;
            }
        }
        $countries = DB::table('pays')->orderBy('nom')->get();
        $level = 'urs';
        return view('urs.update_adherent_club', compact('personne', 'view_type', 'countries', 'level'));
    }

    // mise à jour des informations d'un adhérent par un responsable UR
    public function updatePersonne(PersonneRequest $request, Personne $personne, $view_type) {
        $datap = $request->only('nom', 'prenom', 'datenaissance', 'email', 'phone_mobile', 'sexe');
        $personne->update($datap);

        $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville', 'telephonedomicile');
        $pays = Pays::where('id', $request->pays)->first();
        if ($pays) {
            $dataa['pays'] = $pays->nom;
        }
        $adresse_1 = $personne->adresses[0];
        $adresse_1->update($dataa);

        if (isset($request->villeLivraison) && isset($request->codepostalLivraison) && isset($request->paysLivraison) && isset($personne->adresses[1])) {
            $dataa2 = $request->only('libelle1Livraison', 'libelle2Livraison', 'codepostalLivraison', 'villeLivraison', 'telephonedomicileLivraison');
            $pays = Pays::where('id', $request->paysLivraison)->first();
            if ($pays) {
                $dataa2['pays'] = $pays->nom;
            }
            $adresse_2 = $personne->adresses[1];
            $adresse_2->update($dataa2);
        }
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification de l'adhérent \"".$personne->prenom." ".$personne->nom."\".");
        }
        return redirect('/urs/personnes/'.$view_type)->with('success', "La personne a bien été mise à jour");
    }

    // affichage des informations de l'UR
    public function infosUr()
    {
       $ur =  $this->getUrInformations($this->getUr());
        $countries = Pays::all();
        return view('urs.infos_ur', compact('ur', 'countries'));
    }
    //modification des informations de l'Ur
    public function updateUr(Request $request)
    {
       $ur =$this->getUr();
       $this->updateUrInformations($ur, $request);
        return redirect()->back()->with('success', "Les informations de l'UR ont été mises à jour");
    }

    // affichage de la liste des clubs de l'UR
    public function listeClubs($statut = null, $type_carte = null, $abonnement = null, $term = null) {
        $ur = $this->getUr();
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Club::where('urs_id', $ur->id)->orderBy('numero');
        if (($statut != 'all') && in_array(strval($statut), [0, 1, 2, 3])) {
            $query->where('statut', $statut);
        }
        if ($term) {
            //appel de la fonction getClubByTerm($club, $term) qui retourne les clubs filtrés selon le term
            $this->getClubsByTerm($term, $query);
        }
        if (in_array(strval($abonnement), [0, 1, "G"]) && ($abonnement != 'all')) {
            $query = $query->where('abon', $abonnement);
        }

        if (in_array(strval($type_carte), [1, "N", "C", "A"]) && ($type_carte != 'all')) {
            $query = $query->where('ct', $type_carte);
        }
        $clubs = $query->paginate(100);
        foreach ($clubs as $club) {
            // on récupère le contact
            $contact = DB::table('fonctionsutilisateurs')->join('utilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $club->id)
                ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
                ->first();
            $club->contact = $contact ?? null;
            $club->numero = str_pad($club->numero, 4, "0", STR_PAD_LEFT);
            $club->adresse->callable_mobile = $this->format_phone_number_callable($club->adresse->telephonemobile);
            $club->adresse->visual_mobile = $this->format_phone_number_visual($club->adresse->telephonemobile);
            $club->adresse->callable_fixe = $this->format_phone_number_callable($club->adresse->telephonedomicile);
            $club->adresse->visual_fixe = $this->format_phone_number_visual($club->adresse->telephonedomicile);
        }
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        return view('urs.liste_clubs', compact('ur', 'clubs', 'statut', 'type_carte', 'abonnement', 'term', 'numeroencours'));
    }


    // liste des adhérents d'un club par responsable UR
    public function listeAdherentsClub(Club $club, $statut = null, $abonnement = null)
    {
        $ur = $this->getUr();
        if (!($club->urs_id == $ur->id)) {
            return redirect()->route('accueil')->with('error', "La liste des adhérents du club à laquelle vous avez cherché à accéder n'appartient pas à l'UR que vous gérez");
        }
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        $club->is_abonne = $club->numerofinabonnement >= $numeroencours;
        $club->numero_fin_reabonnement = $club->is_abonne ? $club->numerofinabonnement + 5 : $numeroencours + 5;
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0, 1, 2, 3, 4])) {
            $query = $query->where('utilisateurs.statut', $statut);
        }
        if (in_array($abonnement, [0, 1])) {
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
        return view('urs.liste_adherents_club', compact('ur', 'club', 'adherents', 'statut', 'abonnement'));
    }

    // affichage de la liste des fonctions de l'UR
    public function listeFonctions()
    {
        $ur = $this->getUr();
        $fonctions = Fonction::join('fonctionsurs', 'fonctionsurs.fonctions_id', '=', 'fonctions.id')
            ->where('fonctionsurs.urs_id', $ur->id)
            ->orderBy('fonctions.urs_id')
            ->orderBy('fonctions.id')
            ->selectRaw('fonctions.*')
            ->get();
        foreach ($fonctions as $k => $fonction) {
            $utilisateur = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
                ->whereNotNull('utilisateurs.personne_id')
                ->where('utilisateurs.urs_id', $ur->id)
                ->first();

            if ($utilisateur) {
                $fonction->utilisateur = $utilisateur;
            } else {
                unset($fonctions[$k]);
            }
        }

        return view('urs.fonctions.liste', compact('ur', 'fonctions'));
    }

    // affichage de la liste des reversements de l'UR TODO
    public function listeReversements()
    {
        $ur = $this->getUr();
        return view('urs.liste_reversements', compact('ur'));
    }

    // fonction pour récupérer l'UR de l'adhérent
    protected function getUr()
    {
        $cartes = session()->get('cartes');
        if (!$cartes || count($cartes) == 0) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
        }
        $active_carte = $cartes[0];
        $ur_id = $active_carte->urs_id;
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
        }
        return $ur;
    }

    // affichage des infos club pour un responsable UR
    public function updateClub(Club $club)
    {
        $ur = $this->getUr();
        if (!($club->urs_id == $ur->id)) {
            return redirect()->route('accueil')->with('error', "Le club que vous avez cherché à modifier n'appartient pas à l'UR que vous gérez");
        }
        list($club, $activites, $equipements, $countries) = $this->getClubFormParameters($club);
        return view('urs.update_club', compact('club', 'activites', 'countries', 'equipements'));
    }

    public function updateGeneralite(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubGeneralite($club, $request);
        return redirect()->route('UrGestion_updateClub', compact('club'))->with('success', "Les informations générales du club a été mise à jour");;
    }

    // mise à jour des informations de l'adresse du club par un responsable UR
    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $this->updateClubAdress($club, $request);
        return redirect()->route('UrGestion_updateClub', compact('club'))->with('success', "L'adresse du club a été mise à jour");
    }

    // mise à jour des informations de réunion du club par un responsable UR
    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club, $request);
        return redirect()->route('UrGestion_updateClub', compact('club'))->with('success', "Les informations de réunion du club ont été mises à jour");
    }

    // affichage de la vue pour créer une fonction UR et l'attribuer ou attribuer une fonction général des URs
    public function createFonction()
    {
        $ur = $this->getUr();
        // on liste toutes les fonctions FPF
        $fonctions = Fonction::where('urs_id', 0)->where('instance', 2)->get();
        // on retire toutes les fonctions déjà attribuées à l'UR
        foreach ($fonctions as $k => $fonction) {
            $fonctionur = DB::table('fonctionsurs')->where('fonctions_id', $fonction->id)->where('urs_id', $ur->id)->first();
            if ($fonctionur) {
                unset($fonctions[$k]);
            }
        }
        return view('urs.fonctions.create', compact('ur', 'fonctions'));
    }

    // enregistrement d'une fonction UR et son attribution
    public function storeFonction(Request $request)
    {
        if ($request->fonction_fpf != 0 && $request->libelle != '') {
            return redirect()->route('urs.fonctions.create')->with('error', "Vous devez choisir entre fonction FPF et fonction spécifique");
        }
        if ($request->fonction_fpf == 0 && $request->libelle == '') {
            return redirect()->route('urs.fonctions.create')->with('error', "Vous devez sélectionner une fonction FPF ou saisir une fonction spécifique");
        }
        if ($request->identifiant == '') {
            return redirect()->route('urs.fonctions.create')->with('error', "Vous devez saisir un identifiant");
        }
        $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
        if (!$utilisateur) {
            return redirect()->route('urs.fonctions.create')->with('error', "L'identifiant saisi n'est pas valide");
        }
        if ($utilisateur->urs_id != $this->getUr()->id) {
            return redirect()->route('urs.fonctions.create')->with('error', "L'adhérent doit faire partie de votre UR");
        }

        if ($request->libelle != '') {
            // on crée la fonction spécifique pour l'UR
            $max_ordre = Fonction::where('urs_id', $this->getUr()->id)->where('instance', 2)->max('ordre');
            $dataf = array('libelle' => trim($request->libelle), 'urs_id' => $this->getUr()->id, 'instance' => 2, 'ordre' => $max_ordre + 1);
            $fonction = Fonction::create($dataf);
        } else {
            $fonction = Fonction::where('id', $request->fonction_fpf)->first();
        }
        if (!$fonction) {
            return redirect()->route('urs.fonctions.create')->with('error', "La fonction n'a pas pu être créée");
        }
        // on ajoute la fonction à la table fonctionsurs
        $max_ordre = DB::table('fonctionsurs')->where('urs_id', $this->getUr()->id)->max('ordre');
        $datafu = array('fonctions_id' => $fonction->id, 'urs_id' => $this->getUr()->id, 'ordre' => $max_ordre + 1);
        DB::table('fonctionsurs')->insert($datafu);

        // on ajoute la fonction à la table fonctionsutilisateurs
        $datafu = array('fonctions_id' => $fonction->id, 'utilisateurs_id' => $utilisateur->id);
        DB::table('fonctionsutilisateurs')->insert($datafu);
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Ajout d'une fonction pour votre UR. ");
        }

        return redirect()->route('urs.fonctions.liste')->with('success', "La fonction a été créée");
    }

    // suppression d'une fonction spécifique UR
    public function destroyFonction(Fonction $fonction)
    {
        DB::table('fonctionsurs')->where('fonctions_id', $fonction->id)->delete();
        DB::table('fonctionsutilisateurs')->where('fonctions_id', $fonction->id)->delete();
        $fonction->delete();
        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Suppression de la fonction\"".$fonction->libelle."\" de votre UR.");
        }
        return redirect()->route('urs.fonctions.liste')->with('success', "La fonction a été supprimée");
    }

    // affichage de la vue pour changer l'attribution d'une fonction UR
    public function changeAttribution(Fonction $fonction)
    {
        $ur = $this->getUr();
        return view('urs.fonctions.change_attribution', compact('fonction', 'ur'));
    }

    // changement de l'attribution de la fonction UR
    public function updateFonction(Request $request, Fonction $fonction)
    {
        $ur = $this->getUr();
        if ($request->identifiant == '') {
            return redirect()->route('urs.fonctions.change_attribution', $fonction)->with('error', "Vous devez saisir un identifiant");
        }
        $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
        if (!$utilisateur) {
            return redirect()->route('urs.fonctions.change_attribution', $fonction)->with('error', "L'identifiant saisi n'est pas valide");
        }
        if ($utilisateur->urs_id != $this->getUr()->id) {
            return redirect()->route('urs.fonctions.change_attribution', $fonction)->with('error', "L'adhérent doit faire partie de votre UR");
        }
        // on supprime l'ancienne attribution
        $old_utilisateur = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
            ->whereNotNull('utilisateurs.personne_id')
            ->where('utilisateurs.urs_id', $ur->id)
            ->first();
        DB::table('fonctionsutilisateurs')->where('fonctions_id', $fonction->id)->where('utilisateurs_id', $old_utilisateur->id)->delete();

        // on ajoute la nouvelle attribution
        $datafu = array('fonctions_id' => $fonction->id, 'utilisateurs_id' => $utilisateur->id);
        DB::table('fonctionsutilisateurs')->insert($datafu);

        $user = session()->get('user');
        if ($user) {
            $this->MailAndHistoricize($user,"Modification de l'attribution de la fonction\"".$fonction->libelle."\" de votre UR.");
        }
        return redirect()->route('urs.fonctions.liste')->with('success', "L'attribution de la fonction a été modifiée");
    }

    // affichage des informations d'un adhérent
    public function editAdherent($utilisateur_id)
    {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        $countries = Pays::all();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $club = Club::where('id', $utilisateur->clubs_id)->first();
        if (!$club) {
            return redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de la récupération des informations club");
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
        $prev = 'urs';
        return view('clubs.adherents.edit', compact('club', 'utilisateur', 'countries', 'prev'));
    }

    // mise à jour d'un adhérent
    public function updateAdherent(AdherentRequest $request, $utilisateur_id) {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        if ($this->updateClubAdherent($request, $utilisateur)) {
            $user = session()->get('user');
            if ($user) {
                $this->MailAndHistoricize($user,"Modification de l'adhérent \"".$utilisateur->prenom." ".$utilisateur->nom."\".");
            }
            return redirect()->route('urs.liste_adherents_club', [$utilisateur->clubs_id])->with('success', "Les informations de l'adhérent ont été mises à jour");
        } else {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la mise à jour des informations de l'adhérent");
        }
    }
}
