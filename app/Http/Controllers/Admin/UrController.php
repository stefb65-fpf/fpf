<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Concern\UrTools;
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
    use UrTools;

    public function __construct()
    {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!$this->checkDroit('GESSTR')) {
            return redirect()->route('accueil');
        }
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
        if (!$this->checkDroit('GESSTR')) {
            return redirect()->route('accueil');
        }
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
        if (!$this->checkDroit('GESSTR')) {
            return redirect()->route('accueil');
        }
        $fonctions = Fonction::join('fonctionsurs', 'fonctionsurs.fonctions_id', '=', 'fonctions.id')
            ->where('fonctionsurs.urs_id', $ur->id)
            ->orderBy('fonctions.urs_id')
            ->orderBy('fonctions.id')
            ->selectRaw('fonctions.*')
            ->get();
        foreach ($fonctions as $k => $fonction) {
            $utilisateurs = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
                ->whereNotNull('utilisateurs.personne_id')
                ->where('utilisateurs.urs_id', $ur->id)
                ->get();

            if ($utilisateurs) {
                $fonction->utilisateurs = $utilisateurs;
            } else {
                unset($fonctions[$k]);
            }
        }
        return view('admin.urs.fonctions', compact('ur', 'fonctions'));
    }

    public function changeAttributionUr($fonction_id, $ur_id) {
        $fonction = Fonction::where('id', $fonction_id)->first();
        if (!$fonction) {
            return redirect()->route('urs.index')->with('error', "La fonction n'existe pas");
        }
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('urs.index')->with('error', "L'UR n'existe pas");
        }
        return view('admin.urs.change_attribution', compact('fonction', 'ur'));
    }

    public function manageAttributionUr($fonction_id, $ur_id) {
        $fonction = Fonction::where('id', $fonction_id)->first();
        if (!$fonction) {
            return redirect()->route('urs.index')->with('error', "La fonction n'existe pas");
        }
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('urs.index')->with('error', "L'UR n'existe pas");
        }
        // on cherche tous les utilisateurs ayant cette fonction
        $utilisateurs = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
            ->whereNotNull('utilisateurs.personne_id')
            ->where('utilisateurs.urs_id', $ur->id)
            ->get();

        return view('admin.urs.manage_attribution', compact('fonction', 'ur', 'utilisateurs'));
    }

    public function deleteAttribution(Fonction $fonction, Utilisateur $utilisateur) {
        $ur = Ur::where('id', $utilisateur->urs_id)->first();
        DB::table('fonctionsutilisateurs')
            ->where('fonctions_id', $fonction->id)
            ->where('utilisateurs_id', $utilisateur->id)
            ->delete();
        DB::table('fonctionsurs')
            ->where('fonctions_id', $fonction->id)
            ->where('urs_id', $ur->id)
            ->delete();
        return redirect()->route('admin.urs.fonctions', $ur)->with('success', "L'attribution de la fonction a été supprimée");
    }

    public function deleteAttributionMultiple(Fonction $fonction, Utilisateur $utilisateur) {
        $ur = Ur::where('id', $utilisateur->urs_id)->first();
        DB::table('fonctionsutilisateurs')
            ->where('fonctions_id', $fonction->id)
            ->where('utilisateurs_id', $utilisateur->id)
            ->delete();
        return redirect()->route('admin.urs.fonctions.manage_attribution', [$fonction, $ur])->with('success', "L'attribution de la fonction a été supprimée");
    }



    public function updateFonctionForUr(Request $request, $fonction_id, $ur_id) {
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('urs.index')->with('error', "L'UR n'existe pas");
        }
        $fonction = Fonction::where('id', $fonction_id)->first();
        if (!$fonction) {
            return redirect()->route('urs.index')->with('error', "La fonction n'existe pas");
        }
        $code = $this->updateFonctionUr($request->identifiant, $fonction, $ur->id);
        if ($code == '10') {
            return redirect()->route('admin.urs.fonctions.change_attribution', [$fonction->id, $ur->id])->with('error', "Vous devez saisir un identifiant");
        }
        if ($code == '20') {
            return redirect()->route('admin.urs.fonctions.change_attribution', [$fonction->id, $ur->id])->with('error', "L'identifiant saisi n'est pas valide");
        }
        if ($code == '30') {
            return redirect()->route('admin.urs.fonctions.change_attribution', [$fonction->id, $ur->id])->with('error', "L'adhérent doit faire partie de votre UR");
        }
        return redirect()->route('admin.urs.fonctions', $ur)->with('success', "L'attribution de la fonction a été modifiée");
    }

    public function attribuateFonctionForUr(Request $request, $fonction_id, $ur_id) {
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('urs.index')->with('error', "L'UR n'existe pas");
        }
        $fonction = Fonction::where('id', $fonction_id)->first();
        if (!$fonction) {
            return redirect()->route('urs.index')->with('error', "La fonction n'existe pas");
        }
        $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();

        if (!$utilisateur) {
            return redirect()->route('admin.urs.fonctions.manage_attribution', [$fonction, $ur])->with('error', "L'identifiant saisi n'est pas valide");
        }
        if ($utilisateur->urs_id != $ur_id) {
            return redirect()->route('admin.urs.fonctions.manage_attribution', [$fonction, $ur])->with('error', "L'adhérent doit faire partie de l'UR");
        }
        $datafu = array('fonctions_id' => $fonction->id, 'utilisateurs_id' => $utilisateur->id);
        DB::table('fonctionsutilisateurs')->insert($datafu);
        return redirect()->route('admin.urs.fonctions.manage_attribution', [$fonction, $ur])->with('success', "L'attribution a été effectuée");
    }

    public function destroyFonctionUr($fonction_id, $ur_id) {
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('urs.index')->with('error', "L'UR n'existe pas");
        }
        $fonction = Fonction::where('id', $fonction_id)->first();
        if (!$fonction) {
            return redirect()->route('admin.urs.fonctions', $ur)->with('error', "Impossible de supprimer la fonction");
        }
        DB::table('fonctionsurs')->where('fonctions_id', $fonction->id)->delete();
        DB::table('fonctionsutilisateurs')->where('fonctions_id', $fonction->id)->delete();
        $fonction->delete();
        return redirect()->route('admin.urs.fonctions', $ur)->with('success', "La fonctiona  bien été supprimée");
    }

}
