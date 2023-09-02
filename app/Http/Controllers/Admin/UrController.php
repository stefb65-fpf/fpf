<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Election;
use App\Models\Fonction;
use App\Models\Pays;
use App\Models\Ur;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Table;

class UrController extends Controller
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
        $urs = Ur::orderBy('id')->get();
        foreach ($urs as $ur) {
            $president = DB::table('fonctionsutilisateurs')->join('utilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('fonctionsutilisateurs.fonctions_id', 57)
                ->where('utilisateurs.urs_id', $ur->id)
                ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
                ->first();
            $ur->president = $president ?? null;
            $ur->adresse->callable_mobile = $this->format_phone_number_callable($ur->adresse->telephonemobile);
            $ur->adresse->visual_mobile = $this->format_phone_number_visual($ur->adresse->telephonemobile);
            $ur->adresse->callable_fixe = $this->format_phone_number_callable($ur->adresse->telephonedomicile);
            $ur->adresse->visual_fixe = $this->format_phone_number_visual($ur->adresse->telephonedomicile);
            //changer les url des adresses web
            $ur->web = $this->format_web_url($ur->web);
            $ur->departements = DB::table('departements')->where('urs_id', $ur->id)->get();
        }
        return view('admin.urs.index', compact('urs'));
    }

    public function edit(Ur $ur)
    {
        $ur->departements = DB::table('departements')->where('urs_id', $ur->id)->get();
        $ur->adresse->telephonemobile = $this->format_phone_number_visual($ur->adresse->telephonemobile);
        $ur->adresse->telephonedomicile = $this->format_phone_number_visual($ur->adresse->telephonedomicile);
        $countries = Pays::all();
        $ur->adresse->indicatif_fixe = Pays::where('nom', $ur->adresse->pays)->first()->indicatif;
        return view('admin.urs.edit', compact('ur', 'countries'));
    }

    public function update(Request $request, Ur $ur)
    {
        $countries = Pays::all(); //juste pour repasser l variable à la view
        //les données à mettre à jour dans la table adresse:
        $selected_pays = Pays::where('id', $request->pays)->first();
        $indicatif = $selected_pays->indicatif;
        $datap_adresse = array('libelle1' => $request->libelle1, 'libelle2' => $request->libelle2, 'codepostal' => $request->codepostal, 'ville' => $request->ville, 'telephonedomicile' => $request->telephonedomicile, 'telephonemobile' => $request->telephonemobile);
        $telephonedomicile = $this->format_fixe_for_base($datap_adresse["telephonedomicile"], $indicatif);
        $telephonemobile = $this->format_mobile_for_base($datap_adresse["telephonemobile"], $indicatif);
        if ($telephonedomicile == -1) {
            return redirect()->route('urs.edit', compact('ur', 'countries'))->with('error', "Le téléphone fixe est incorrect");
        }
        if ($telephonemobile == -1) {
            return redirect()->route('urs.edit', compact('ur', 'countries'))->with('error', "Le téléphone mobile est incorrect");
        }

        $datap_adresse["telephonedomicile"] = $telephonedomicile;
        $datap_adresse["telephonemobile"] = $telephonemobile;

        $datap_adresse['pays'] = $selected_pays->nom;
        //les données à mettre à jour dans la table ur
        $datap_gen = array('nom' => $request->nom, 'courriel' => $request->courriel, 'web' => $request->web);
        //on met à jour
        $ur->adresse->update($datap_adresse);
        $ur->update($datap_gen);
        return redirect()->route('urs.edit', compact('ur', 'countries'))->with('success', "Vous avez modifié les informations de cette UR avec succès");
    }

    public function fonctions(Ur $ur) {
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
        return view('admin.urs.fonctions', compact('ur', 'fonctions'));
    }

}
