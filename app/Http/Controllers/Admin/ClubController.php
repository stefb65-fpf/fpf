<?php

namespace App\Http\Controllers\Admin;

use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Models\Abonnement;
use App\Models\Activite;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Equipement;
use App\Models\Pays;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function index($ur_id = null, $statut = null, $type_carte = null, $abonnement = null)
    {
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        //TODO: régler le problème de rendre du paginate (la methode render() du blade réponds en erreur si la limite de pagination est supérieure au nombre de clubs dans $clubs!
        $limit_pagination = 100;
        $clubs = Club::orderBy('numero')->paginate($limit_pagination);
        $lur = Ur::where('id',$ur_id)->first();
        if ($ur_id != 'all' &&$lur) {
                $clubs  = $clubs->where('urs_id', $lur->id);
        }
        if ($statut != 'all' && in_array(strval($statut),[0,1,2,3])) {
                $clubs  = $clubs->where('statut', $statut);
        }
        if ($abonnement != 'all' && in_array(strval($abonnement),[0,1,"G"])) {
                $clubs  = $clubs->where('abon', $abonnement);
        }
        if ($type_carte != 'all' && (in_array(strval($type_carte),[1,"N","C","A"]))) {
                $clubs  = $clubs->where('ct', $type_carte);
        }
        foreach ($clubs as $club) {
            // on récupère le contact
            $contact = DB::table('fonctionsutilisateurs')->join('utilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $club->id)
                ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
                ->first();
            $club->contact = $contact ?? null;
            $club->numero = str_pad($club->numero, 4,'0', STR_PAD_LEFT);
            $club->urs_id = str_pad($club->urs_id , 2,'0', STR_PAD_LEFT);
//            dd($club);
            $club->adresse->callable_mobile = $this->format_phone_number_callable($club->adresse->telephonemobile);
            $club->adresse->visual_mobile = $this->format_phone_number_visual($club->adresse->telephonemobile);    $club->adresse->callable_fixe = $this->format_phone_number_callable($club->adresse->telephonedomicile);
            $club->adresse->visual_fixe = $this->format_phone_number_visual($club->adresse->telephonedomicile);
            //changer les url des adresses web
        }
        $urs = Ur::orderBy('nom')->get();

        return view('admin.clubs.index',compact('clubs','urs','ur_id','statut','type_carte','abonnement','limit_pagination'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clubs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeGeneralite(Request $request)
    {
        dd($request);
        return view('admin.clubs.create');
    }
    public function storeAddress(Request $request)
    {
        dd($request);
        return view('admin.clubs.create');
    }
    public function storeReunion(Request $request)
    {
        dd($request);
        return view('admin.clubs.create');
    }
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
    public function update( Club $club)
    {

        list($club, $activites, $equipements, $countries) = $this->getClubFormParameters($club);
        return view('admin.clubs.update',compact('club','activites', 'equipements', 'countries'));
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
        //TODO : enregistrer file sur le serveur
 $this->updateClubGeneralite($club, $request);
        return redirect()->route('FPFGestion_updateClub',compact('club'))->with('success', "Les informations générales du club a été mise à jour");;
    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
       $this->updateClubAdress($club,$request);
        return redirect()->route('FPFGestion_updateClub',compact('club'))->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
     $this->updateClubReunion($club, $request);
        return redirect()->route('FPFGestion_updateClub',compact('club'))->with('success', "Les informations de réunion du club ont été mises à jour");
    }
    public function listeAdherent(Club $club, $statut = null,$abonnement = null){
//        dd($club);
        $statut = $statut ?? "all";
        $abonnement = $abonnement ?? "all";
        $query = Utilisateur::join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
            ->where('utilisateurs.clubs_id', $club->id)->orderBy('utilisateurs.identifiant');
        if (in_array($statut, [0,1,2,3,4])) {
            $query = $query->where('utilisateurs.statut', $statut);
        }
        if (in_array($abonnement, [0,1])) {
            $query = $query->where('personnes.is_abonne', $abonnement);
        }
        $adherents = $query->get();
        foreach ($adherents as $adherent) {
            // si la personne est abonnée, on récupère le numéro de fin de son abonnement
            $adherent->fin = $adherent->personne->is_abonne ? $adherent->personne->abonnements->where('etat', 1)[1]['fin'] : '';
        }
        return view('admin.clubs.liste_adherents_club',compact('club','adherents', 'statut','abonnement'));
    }
}
