<?php

namespace App\Http\Controllers\Admin;

use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Exports\RoutageFedeExport;
use App\Exports\RoutageListAdherents;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddClubRequest;
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

        return view('admin.clubs.index',compact('clubs','urs','ur_id','statut','type_carte','abonnement','term'));
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
            return redirect()->route('admin.clubs.create')->with('error', "Un club avec cette adresse email existe déjà");
        }
        // on vérifie qu'il n'y a pas une personne avec l'adresse email du contact
        $personne = Personne::where('email', $request->emailContact)->first();
        if ($personne) {
            return redirect()->route('admin.clubs.create')->with('error', "Une personne avec l'adresse email saisie pour le contact existe déjà");
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
                'telephonemobile' => $this->format_mobile_for_base($request->phoneMobileClub, $pays->indicatif),
            ];
            if ($request->phoneFixeClub != '') {
                $dataa['telephonedomicile'] = $this->format_fixe_for_base($request->phoneFixeClub, $pays->indicatif);
            }
            $adresseClub = Adresse::create($dataa);
            if (!$adresseClub) {

            }

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
                $dataac['telephonedomicile'] = $this->format_fixe_for_base($request->phoneFixeContact, $paysContact->indicatif);
            }
            $adresseContact = Adresse::create($dataac);

            // on crée la personne contact
            $datapc = [
                'nom' => strtoupper($request->nomContact),
                'prenom' => ucfirst($request->prenomContact),
                'sexe' => $request->sexeContact,
                'email' => $request->emailContact,
                'password' => $this->generateRandomPassword(),
                'phone_mobile' => $phoneMobileContact,
                'is_adherent' => 1
            ];
            $personne = Personne::create($datapc);

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

    /**
     * Store a newly created resource in storage.
     */
//    public function storeGeneralite(Request $request)
//    {
//        dd($request);
//        return view('admin.clubs.create');
//    }
//    public function storeAddress(Request $request)
//    {
//        dd($request);
//        return view('admin.clubs.create');
//    }
//    public function storeReunion(Request $request)
//    {
//        dd($request);
//        return view('admin.clubs.create');
//    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Club $club)
    {

        list($club, $activites, $equipements, $countries) = $this->getClubFormParameters($club);
        return view('admin.clubs.edit',compact('club','activites', 'equipements', 'countries'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function updateGeneralite(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubGeneralite($club, $request);
        return redirect()->route('admin.clubs.edit', $club)->with('success', "Les informations générales du club a été mise à jour");;
    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $this->updateClubAdress($club,$request);
        return redirect()->route('admin.clubs.edit', $club)->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club, $request);
        return redirect()->route('admin.clubs.edit', $club)->with('success', "Les informations de réunion du club ont été mises à jour");
    }
    public function listeAdherent(Club $club, $statut = null,$abonnement = null){
//        dd($club);
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant')
            ->selectRaw('*, utilisateurs.id as id_utilisateur');
        if (in_array($statut, [0,1,2,3,4])) {
            $query = $query->where('utilisateurs.statut', $statut);
        }
        if (in_array($abonnement, [0,1])) {
            $query = $query->where('personnes.is_abonne', $abonnement);
        }
        $adherents = $query->get();
        foreach ($adherents as $adherent) {
            // si la personne est abonnée, on récupère le numéro de fin de son abonnement
            if($adherent->personne->is_abonne ){
                $adherent->fin = "";

                if($adherent->personne->abonnements->where('etat', 1) && isset($adherent->personne->abonnements->where('etat', 1)[0])){
                    $adherent->fin = $adherent->personne->abonnements->where('etat', 1)[0]['fin'];
                }elseif ($adherent->personne->abonnements->where('etat', 1) &&  isset($adherent->personne->abonnements->where('etat', 1)[1])){
                    $adherent->fin = $adherent->personne->abonnements->where('etat', 1)[1]['fin'];
                }
            }
//            $adherent->fin = $adherent->personne->is_abonne ? $adherent->personne->abonnements->where('etat', 1)[0]['fin'] : '';
        }

        return view('admin.clubs.liste_adherents_club',compact('club','adherents', 'statut','abonnement'));
    }

}
