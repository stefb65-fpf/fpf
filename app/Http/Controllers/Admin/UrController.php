<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Pays;
use App\Models\Ur;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $urs = Ur::orderBy('id')->get();
        foreach($urs as $ur) {
            $president = DB::table('fonctionsutilisateurs')->join('utilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('fonctionsutilisateurs.fonctions_id', 57)
                ->where('utilisateurs.urs_id', $ur->id)
                ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
                ->first();
            $ur->president = $president ?? null;
            $ur->adresse->callable_mobile = $this->format_phone_number_callable($ur->adresse->telephonemobile);
            $ur->adresse->visual_mobile = $this->format_phone_number_visual($ur->adresse->telephonemobile);    $ur->adresse->callable_fixe = $this->format_phone_number_callable($ur->adresse->telephonedomicile);
            $ur->adresse->visual_fixe = $this->format_phone_number_visual($ur->adresse->telephonedomicile);
            //changer les url des adresses web
            $ur->web = $this->format_web_url($ur->web);
        }
        return view('admin.urs.index', compact('urs'));
    }
    public function urEdit(Ur $ur) {
        $ur->adresse->telephonemobile = $this->format_phone_number_visual($ur->adresse->telephonemobile);
        $ur->adresse->telephonedomicile = $this->format_phone_number_visual($ur->adresse->telephonedomicile);
        $countries = Pays::all();

        $ur->adresse->indicatif_fixe = Pays::where('nom', $ur->adresse->pays)->first()->indicatif;
        return view('admin.urs.edit', compact('ur','countries'));
    }

    public function updateUr(Request $request,Ur $ur)
    {
        $countries = Pays::all(); //juste pour repasser l variable à la view
        //les données à mettre à jour dans la table adresse:
        $selected_pays = Pays::where('id', $request->pays)->first();
        $indicatif = $selected_pays->indicatif;
        $datap_adresse =   array('libelle1' => $request->libelle1,'libelle2'=>$request->libelle2,'codepostal'=>$request->codepostal,'ville'=>$request->ville,'telephonedomicile'=>$request->telephonedomicile,'telephonemobile'=>$request->telephonemobile) ;
            $datap_adresse["telephonedomicile"] =$this->format_fixe_for_base($datap_adresse["telephonedomicile"],$indicatif) ;
        $datap_adresse["telephonemobile"] =$this->format_mobile_for_base($datap_adresse["telephonemobile"]);
                $datap_adresse['pays'] = $selected_pays->nom;
//        dd($datap_adresse);
        //les données à mettre à jour dans la table ur
        $datap_gen=   array('nom' => $request->nom,'courriel' => $request->courriel,'web' => $request->web);
//        dd($datap_gen);
        //on met à jour
        $ur->adresse->update($datap_adresse);
        $ur->update($datap_gen);
//        return view('admin.urs.edit', compact('ur','countries'))->with('success','Vous avez modifié les informations d\'UR avec succès');
        return redirect()->route('urs.edit',compact('ur','countries'))->with('success', "Vous avez modifié les informations de cette UR avec succès");
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
