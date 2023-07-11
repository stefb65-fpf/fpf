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

        if(!$statut){
            $statut = "all";
        }
        if(!$abonnement){
            $abonnement = "all";
        }
        $clubs = Club::orderBy('numero')->paginate(100);
        if (($ur_id != null) && ($ur_id != 'all')) {
            //verifier que le parametre envoyé existe
            $lur = Ur::where('id',$ur_id)->first();
            if ($lur) {
                $clubs  = $clubs->where('urs_id', $lur->id);
            }
        }
        if (($statut != null) && ($statut != 'all')) {
            //verifier que le parametre envoyé existe
            $lestatut = in_array($statut,[0,1,2,3]);
            if ($lestatut) {
                $clubs  = $clubs->where('statut', $statut);
            }
        }
//        dd($clubs);
        foreach ($clubs as $club) {
            // on récupère le contact
            $contact = DB::table('fonctionsutilisateurs')->join('utilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $club->id)
                ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
                ->first();
            $club->contact = $contact ?? null;
            $club->numero = $this->complement_string_to($club->numero, 4);
            $club->urs_id = $this->complement_string_to($club->urs_id , 2);
//            dd($club);
            $club->adresse->callable_mobile = $this->format_phone_number_callable($club->adresse->telephonemobile);
            $club->adresse->visual_mobile = $this->format_phone_number_visual($club->adresse->telephonemobile);    $club->adresse->callable_fixe = $this->format_phone_number_callable($club->adresse->telephonedomicile);
            $club->adresse->visual_fixe = $this->format_phone_number_visual($club->adresse->telephonedomicile);
            //changer les url des adresses web
        }
        $urs = Ur::orderBy('nom')->get();

        return view('admin.clubs.index',compact('clubs','urs',"ur_id","statut","type_carte","abonnement"));
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
       $result = $this->getClubFormParameters($club);
        $club = $result[0];
        $activites = $result[1];
        $equipements = $result[2];
        $countries = $result[3];
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
        return redirect()->route('updateClub',compact('club'))->with('success', "Les informations générales du club a été mise à jour");;
    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
       $this->updateClubAdress($club,$request);
        return redirect()->route('updateClub',compact('club'))->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
     $this->updateClubReunion($club, $request);
        return redirect()->route('updateClub',compact('club'))->with('success', "Les informations de réunion du club ont été mises à jour");
    }

}
