<?php

namespace App\Http\Controllers\Admin;

use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Exports\RoutageFedeExport;
use App\Exports\RoutageListAdherents;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddClubRequest;
use App\Http\Requests\AdherentRequest;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Models\Abonnement;
use App\Models\Activite;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Equipement;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Tarif;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    use Tools;
    use ClubTools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index($ur_id = null, $statut = null, $type_carte = null, $abonnement = null, $term = null)
    {
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Club::orderBy('numero');
        if($term){
            //appel de la fonction getClubByTerm($club, $term) qui retourne les clubs filtrés selon le term
            $this->getClubsByTerm($term,$query);
        }
        $lur = Ur::where('id',$ur_id)->first();
        if ($ur_id != 'all' &&$lur) {
            $query  = $query->where('urs_id', $lur->id);
        }
        if ($statut != 'all' && in_array(strval($statut),[0,1,2,3])) {
            $query  = $query->where('statut', $statut);
        }
        if ($abonnement != 'all' && in_array(strval($abonnement),[0,1,"G"])) {
            $query  = $query->where('abon', $abonnement);
        }
        if ($type_carte != 'all' && (in_array(strval($type_carte),[1,"N","C","A"]))) {
            $query  = $query->where('ct', $type_carte);
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
            $club->numero = str_pad($club->numero, 4,'0',STR_PAD_LEFT);
            $club->urs_id = str_pad($club->urs_id , 2,'0',STR_PAD_LEFT);
//            dd($club);
            $club->adresse->callable_mobile = $this->format_phone_number_callable($club->adresse->telephonemobile);
            $club->adresse->visual_mobile = $this->format_phone_number_visual($club->adresse->telephonemobile);    $club->adresse->callable_fixe = $this->format_phone_number_callable($club->adresse->telephonedomicile);
            $club->adresse->visual_fixe = $this->format_phone_number_visual($club->adresse->telephonedomicile);
            //changer les url des adresses web
        }
        $urs = Ur::orderBy('nom')->get();
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;

        return view('admin.clubs.index',compact('clubs','urs','ur_id','statut','type_carte','abonnement','term', 'numeroencours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $urs = Ur::orderBy('id')->get();
        $countries = Pays::orderBy('nom')->get();
        return view('admin.clubs.create', compact('urs', 'countries'));
    }

    public function store(AddClubRequest $request) {
        // on vérifie qu'il n'y a pas de club avec cette adresse email
        $club = Club::where('courriel', $request->emailClub)->first();
        if ($club) {
//            return redirect()->route('admin.clubs.create')->with('error', "Un club avec cette adresse email existe déjà");
            return redirect()->back()->with('error', "Un club avec cette adresse email existe déjà")->withInput();
        }
        // on vérifie qu'il n'y a pas une personne avec l'adresse email du contact
        $personne = Personne::where('email', $request->emailContact)->first();
        if ($personne) {
//            return redirect()->route('admin.clubs.create')->with('error', "Une personne avec l'adresse email saisie pour le contact existe déjà");
            return redirect()->back()->with('error', "Une personne avec l'adresse email saisie pour le contact existe déjà")->withInput();
        }
        // on cherche le dernier numéro de club pour l'UR saisie
        $numero = Club::where('urs_id', $request->urClub)->max('numero');
        $new_numero = $numero + 1;

        try {
            // on crée l'adresse pour le club
            DB::beginTransaction();
            $pays = Pays::where('id', $request->paysClub)->first();
            $dataa = [
                'libelle1' => $request->libelle1Club,
                'libelle2' => $request->libelle2Club,
                'codepostal' => $request->codepostalClub,
                'ville' => strtoupper($request->villeClub),
                'pays' => strtoupper($pays->nom),
//                'telephonemobile' => $this->format_mobile_for_base($request->phoneMobileClub, $pays->indicatif),
            ];
            $telephonemobile = $this->format_mobile_for_base($request->phoneMobileClub, $pays->indicatif);
            if ($telephonemobile == -1) {
                DB::rollBack();
                return redirect()->back()->with('error', "Le numéro de téléphone mobile du club n'est pas valide")->withInput();
            }
            $dataa['telephonemobile'] = $telephonemobile;
            if ($request->phoneFixeClub != '') {
                $telephonedomicile = $this->format_fixe_for_base($request->phoneFixeClub, $pays->indicatif);
                if ($telephonedomicile == -1) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Le numéro de téléphone domicile du club n'est pas valide")->withInput();
                }
                $dataa['telephonedomicile'] = $telephonedomicile;
            }
            $adresseClub = Adresse::create($dataa);

            // on crée le club
            $club_abonne = isset($request->abonClub) ? 1 : 0;
            $datac = [
                'numero' => $new_numero,
                'nom' => $request->nomClub,
                'courriel' => $request->emailClub,
                'abon' => $club_abonne,
                'ct' => 'N',
                'statut' => 1,
                'urs_id' => $request->urClub,
                'adresses_id' => $adresseClub->id,
            ];
            $club = Club::create($datac);

            // on crée l'adresse pour le contact
            $paysContact = Pays::where('id', $request->paysContact)->first();
            $phoneMobileContact = $this->format_mobile_for_base($request->phoneMobileContact, $paysContact->indicatif);
            if ($phoneMobileContact == -1) {
                DB::rollBack();
                return redirect()->back()->with('error', "Le numéro de téléphone mobile du contact n'est pas valide")->withInput();
            }
            $contact_abonne = isset($request->abonContact) ? 1 : 0;
            $dataac = [
                'libelle1' => $request->libelle1Contact,
                'libelle2' => $request->libelle2Contact,
                'codepostal' => $request->codepostalContact,
                'ville' => strtoupper($request->villeContact),
                'pays' => strtoupper($paysContact->nom),
                'telephonemobile' => $phoneMobileContact,
            ];
            if ($request->phoneFixeContact != '') {
                $phoneFixeContact = $this->format_fixe_for_base($request->phoneFixeContact, $paysContact->indicatif);
                if ($phoneFixeContact == -1) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Le numéro de téléphone fixe du contact n'est pas valide")->withInput();
                }
                $dataac['telephonedomicile'] = $phoneFixeContact;
            }
            $adresseContact = Adresse::create($dataac);

            // on crée la personne contact
            $password = $this->generateRandomPassword();
            $firstname = ucfirst($request->prenomContact);
            $lastname = strtoupper($request->nomContact);
            $datapc = [
                'nom' => $lastname,
                'prenom' => $firstname,
                'sexe' => $request->sexeContact,
                'email' => $request->emailContact,
                'password' => $password,
                'phone_mobile' => $phoneMobileContact,
                'is_adherent' => 1
            ];
            $personne = Personne::create($datapc);

            $this->insertWpUser($firstname, $lastname, $request->emailContact, $password);

            // on lie l'adresse à la personne
            $personne->adresses()->attach($adresseContact->id);

            // on crée ladhérent dans la table utilisateurs
            $identifiant = str_pad($request->urClub, 2, '0', STR_PAD_LEFT) . '-' . str_pad($club->numero, 4, '0', STR_PAD_LEFT) . '-0001';
            $datau = [
                'urs_id' => $request->urClub,
                'clubs_id' => $club->id,
                'adresses_id' => $personne->adresses[0]->id,
                'personne_id' => $personne->id,
                'identifiant' => $identifiant,
                'numeroutilisateur' => 1,
                'sexe' => $request->sexeContact,
                'nom' => strtoupper($request->nomContact),
                'prenom' => ucfirst($request->prenomContact),
                'ct' => 2,
                'statut' => 1,
                'saison' => date('Y'),
            ];
            $utilisateur = Utilisateur::create($datau);

            // on ajoute le fonction contact dans la table fonctionsutilisateurs
            DB::table('fonctionsutilisateurs')->insert([
                'utilisateurs_id' => $utilisateur->id,
                'fonctions_id' => 97
            ]);

            // on crée le règlement pour le club
            $ref = date('y') . '-' . $identifiant;
            $tarif_adh_club = Tarif::where('statut', 0)->where('id', 4)->first();
            $montant_reglement = $tarif_adh_club->tarif;
            if ($club_abonne) {
                $tarif_abo_club = Tarif::where('statut', 0)->where('id', 5)->first();
                $montant_reglement += $tarif_abo_club->tarif;
            }
            $tarif_adh_contact = Tarif::where('statut', 0)->where('id', 8)->first();
            $montant_reglement += $tarif_adh_contact->tarif;
            if ($contact_abonne) {
                $tarif_abo_contact = Tarif::where('statut', 0)->where('id', 17)->first();
                $montant_reglement += $tarif_abo_contact->tarif;
            }
            $datar = [
                'clubs_id' => $club->id,
                'montant' => $montant_reglement,
                'statut' => 0,
                'reference' => $ref,
                'aboClub' => $club_abonne,
                'adhClub' => 1
            ];
            $reglement = Reglement::create($datar);

            // on insère dans la table reglementsutilisateurs
            $dataru = [
                'reglements_id' => $reglement->id,
                'utilisateurs_id' => $utilisateur->id,
                'adhesion' => 1,
                'abonnement' => $contact_abonne
            ];
            DB::table('reglementsutilisateurs')->insert($dataru);

            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', "Le club $club->numero a été créé. Pour valider l'inscription, vous devez valider le règlement $ref d'un montant de $montant_reglement €");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.clubs.create')->with('error', "Une erreur est survenue lors de la création du club");
        }
    }

    public function edit(Club $club)
    {
        list($club, $activites, $equipements, $countries) = $this->getClubFormParameters($club);
        return view('admin.clubs.edit',compact('club','activites', 'equipements', 'countries'));
    }

    public function createAdherent($club_id) {
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return redirect()->route('admin.clubs.liste_adherents_club', $club_id)->with('error', "Un problème est survenu lors de la récupération des informations club");
        }
        $utilisateur = new Utilisateur();
        $personne = new Personne();
        $adresse = new Adresse();
        $adresse->pays = "france";
        $adresse->indicatif = "33";
        $utilisateur->personne = $personne;
        $utilisateur->personne->adresses = [$adresse];
        $countries = Pays::all();
        $prev = 'admin.clubs';
        return view('clubs.adherents.create', compact('club', 'countries', 'utilisateur', 'prev'));
    }

    public function storeAdherent(Request $request, $club_id) {
        $club = Club::where('id', $club_id)->first();
        if (!$club) {
            return redirect()->route('admin.clubs.liste_adherents_club', $club_id)->with('error', "Un problème est survenu lors de la récupération des informations club");
        }

        $code = $this->storeClubAdherent($request, $club);
        if ($code == '0') {
            return redirect()->route('admin.clubs.liste_adherents_club', $club_id)->with('success', "L'adhérent a bien  été ajouté");
        } else {
            return match ($code) {
                '1' => redirect()->back()->with('error', "Problème lors de la récupérartion des informations de la personne")->withInput(),
                '2' => redirect()->back()->with('error', "L'adresse email est invalide")->withInput(),
                '3' => redirect()->back()->with('error', "Le pays est invalide")->withInput(),
                '4' => redirect()->back()->with('error', "Téléphone mobile invalide")->withInput(),
                '5' => redirect()->back()->with('error', "Téléphone fixe invalide")->withInput(),
                default => redirect()->route('admin.clubs.liste_adherents_club', $club_id)->with('error', "Un problème est survenu lors de l'ajout de l'adhérent"),
            };
        }
    }

    public function editAdherent($utilisateur_id) {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        $countries = Pays::all();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $club = Club::where('id', $utilisateur->clubs_id)->first();
        if (!$club) {
            return redirect()->route('clubs.adherents.index')->with('error', "Un problème est survenu lors de la récupération des informations club");
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
        $prev = 'admin';
        return view('clubs.adherents.edit', compact('club', 'utilisateur', 'countries', 'prev'));
    }

    public function updateAdherent(AdherentRequest $request, $utilisateur_id) {
        $utilisateur = Utilisateur::where('id', $utilisateur_id)->first();
        if (!$utilisateur) {
            return redirect()->route('clubs.adherents.edit', $utilisateur_id)->with('error', "Un problème est survenu lors de la récupération des informations utilisateur");
        }
        $code = $this->updateClubAdherent($request, $utilisateur);
        if ($code == '01') {
            return redirect()->route('admin.clubs.liste_adherents_club', [$utilisateur->clubs_id])->with('success', "Les informations de l'adhérent ont été mises à jour");
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

    public function updateGeneralite(ClubReunionRequest $request, Club $club)
    {
//        $this->updateClubGeneralite($club, $request);
        $error = $this->updateClubGeneralite($club, $request);
       if($error == 1){
           return redirect()->back()->with('error', "L'image n'est pas au bon format. Veuillez télécharger une image au format .jpeg, .jpg ou .png");
       }elseif( $error == 2){
           return redirect()->back()->with('error', "L'image est trop grande. Veuillez télécharger une image de taille maximum de 1 Mo ");
       }
        return redirect()->route('admin.clubs.edit', $club)->with('success', "Les informations générales du club a été mise à jour");;
    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $code = $this->updateClubAdress($club,$request);
        if ($code == 1) {
            return redirect()->route('admin.clubs.edit', $club)->with('error', "Le téléphone mobile est incorrect");
        }
        if ($code == 2) {
            return redirect()->route('admin.clubs.edit', $club)->with('error', "Le téléphone fixe est incorrect");
        }
        return redirect()->route('admin.clubs.edit', $club)->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club, $request);
        return redirect()->route('admin.clubs.edit', $club)->with('success', "Les informations de réunion du club ont été mises à jour");
    }
    public function listeAdherent(Club $club, $statut = null,$abonnement = null){
        $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
        $club->is_abonne = $club->numerofinabonnement >= $numeroencours;
        $club->numero_fin_reabonnement = $club->is_abonne ? $club->numerofinabonnement + 5 : $numeroencours + 5;
        $statut = $statut ?? "init";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0,1,2,3,4])) {
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
//            $adherent->fin = $adherent->personne->is_abonne ? $adherent->personne->abonnements->where('etat', 1)[0]['fin'] : '';
        }

        return view('admin.clubs.liste_adherents_club',compact('club','adherents', 'statut', 'abonnement'));
    }

}
