<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Personne;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class PersonneController extends Controller
{
    use Tools;
    public function __construct()
    {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.personnes.index');
    }

    public function listeAdherents()
    {

        return view('admin.liste_adherents');
    }

    public function listeUtilisateurs($view_type,  $ur_id = null,$statut = null,$type_carte = null, $type_adherent = null, $term = null)
    {
//        dd($ur_id,$statut)
        $statut = $statut ?? "all";
        $type_adherent = $type_adherent ?? "all";
        $ur=null;
        $limit_pagination=100;
        if($view_type == "formateurs"){
            //TODO : on affiche les formateurs
            $query = Personne::where('is_adherent',0)->where('is_formateur' ,'!=', 0);
        }elseif ($view_type == "abonnes"){
            $query = Personne::where('is_adherent',0)->where('is_abonne' ,'!=', 0);

        }elseif($view_type == "ur_adherents"){
            $cartes = session()->get('cartes');
            if (!$cartes || count($cartes) == 0) {
                return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
            }
            $active_carte = $cartes[0];
            $ur = Ur::where('id', $active_carte->urs_id)->first();
            if (!$ur || ($active_carte->urs_id != $ur_id)) {
                return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
            }
            $query = Utilisateur::where('urs_id' ,'=',$ur->id)->where('personne_id' ,'!=', null);
        }elseif($view_type == "recherche") {
            $query = Personne::join('utilisateurs', 'utilisateurs.personne_id','=','personnes.id' );
        }else{
            $query = Utilisateur::where('urs_id' ,'!=',null)->where('personne_id' ,'!=', null);
        }

        if($term){
            //appel de la fonction getPersonsByTerm($club, $term) qui retourne les personnes filtrées selon le term
            $this->getPersonsByTerm($term,$query);
        }
        if ($ur_id != 'all' && $ur_id) {
            $lur = Ur::where('id',$ur_id)->first();
//            dd($lur->id);
            $query  = $query->where('utilisateurs.urs_id','=', $lur->id);
        }


        if ($statut != 'all' && in_array(strval($statut),[0,1,2,3,4])) {
            $query  = $query->where('statut', $statut);
        }
        if ($type_adherent != 'all' && in_array(strval($type_adherent),[1,2])) {
            if($type_adherent == 1){
                $query  = $query->where('clubs_id', null);
            }elseif($type_adherent == 2){
                $query  = $query->where('clubs_id','!=', null);
            }
        }
        if ($type_carte != 'all' && (in_array(strval($type_carte),[2,3,4,5,6,7,8,9,"F"]))) {
            $query  = $query->where('ct', $type_carte);
        }
        $utilisateurs = $query->paginate($limit_pagination);
        $urs = Ur::orderBy('nom')->get();
        return view('admin.personnes.liste_personnes_template', compact('view_type', 'utilisateurs', 'statut', 'type_carte', 'type_adherent','ur_id','urs','ur', 'term','limit_pagination'));
    }

    public function listeAbonnes()
    {
        return view('admin.personnes.liste_abonnes');
    }

    public function listeFormateurs()
    {
        return view('admin.personnes.liste_formateurs');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
