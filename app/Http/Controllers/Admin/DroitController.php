<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Droit;
use App\Models\Fonction;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class DroitController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // on vérifie que l'utilisateur a le droit d'accéder à cette page
        if (!$this->checkDroit('GESDRO')) {
            return redirect()->route('accueil');
        }
        $droits = Droit::orderBy('position')->get();
        $fonctions = Fonction::where('instance', 1)->orderBy('ordre')->selectRaw('libelle, id')->get();
        $fonctions_urs = Fonction::where('instance', 2)->orderBy('ordre')->selectRaw('libelle, id')->get();
        return view('admin.droits.index', compact('droits', 'fonctions', 'fonctions_urs'));
    }

    public function deleteUtilisateur($droit_id, $utilisateurs_id) {
        // on vérifie que l'utilisateur a le droit d'accéder à cette page
        if (!$this->checkDroit('GESDRO')) {
            return redirect()->route('accueil');
        }
        $droit = Droit::where('id', $droit_id)->first();
        if (!$droit) {
            return redirect()->route('droits.index')->with('error', 'Droit non trouvé');
        }
        $droit->utilisateurs()->detach($utilisateurs_id);
        return redirect()->route('droits.index')->with('success', 'Le droit a été supprimé pour l\'utilisateur');
    }

    public function deleteFonction($droit_id, $fonction_id) {
        // on vérifie que l'utilisateur a le droit d'accéder à cette page
        if (!$this->checkDroit('GESDRO')) {
            return redirect()->route('accueil');
        }
        $droit = Droit::where('id', $droit_id)->first();
        if (!$droit) {
            return redirect()->route('droits.index')->with('error', 'Droit non trouvé');
        }
        $droit->fonctions()->detach($fonction_id);
        return redirect()->route('droits.index')->with('success', 'Le droit a été supprimé pour la fonction');
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
        // on vérifie que l'utilisateur a le droit d'accéder à cette page
        if (!$this->checkDroit('GESDRO')) {
            return redirect()->route('accueil');
        }
        if ($request->fonction == -1 && $request->utilisateur == '') {
            return redirect()->route('droits.index')->with('error', 'Veuillez choisir une fonction ou un utilisateur');
        }
        if ($request->fonction != -1 && $request->utilisateur != '') {
            return redirect()->route('droits.index')->with('error', 'Veuillez choisir seulement une fonction ou un utilisateur');
        }
        if ($request->utilisateur != '' && strlen($request->utilisateur) !== 12) {
            return redirect()->route('droits.index')->with('error', 'Veuillez indiquer un identifiant FPF valide');
        }
        if ($request->utilisateur != '') {
            $utilisateur = Utilisateur::where('identifiant', $request->utilisateur)->first();
            if (!$utilisateur) {
                return redirect()->route('droits.index')->with('error', 'Utilisateur non trouvé');
            }

            // on insère le droit pour l'utilisateur
            $droit = Droit::where('id', $request->droit)->first();
            if (!$droit) {
                return redirect()->route('droits.index')->with('error', 'Droit non trouvé');
            }
            $droit->utilisateurs()->attach($utilisateur->id);
            return redirect()->route('droits.index')->with('success', 'Le droit a été ajouté pour l\'utilisateur');
        }

        if ($request->fonction != -1) {
            $fonction = Fonction::where('id', $request->fonction)->first();
            if (!$fonction) {
                return redirect()->route('droits.index')->with('error', 'Fonction non trouvée');
            }
            // on insère le droit pour la fonction
            $droit = Droit::where('id', $request->droit)->first();
            if (!$droit) {
                return redirect()->route('droits.index')->with('error', 'Droit non trouvé');
            }
            $droit->fonctions()->attach($fonction->id);
            return redirect()->route('droits.index')->with('success', 'Le droit a été ajouté pour la fonction');
        }

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

    protected function checkDroit($strdroit)
    {
        $cartes = session()->get('cartes');
        $user = session()->get('user');
        if ($user->is_administratif) {
            return true;
        }
        if (!isset($cartes[0])) {
            return false;
//            return redirect()->route('accueil');
        }
        $droits = [];
        foreach($cartes[0]->droits as $droit) {
            $droits[] = $droit->label;
        }
        foreach ($cartes[0]->fonctions as $fonction) {
            foreach ($fonction->droits as $droit) {
                $droits[] = $droit->label;
            }
        }
        if (!in_array($strdroit, $droits)) {
            return false;
//            return redirect()->route('accueil');
        }
        return true;
    }
}
