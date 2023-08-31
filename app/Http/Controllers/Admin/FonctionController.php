<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FonctionRequest;
use App\Models\Fonction;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FonctionController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess'])->except(['updateFonctionCe']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin_fonctions = Fonction::where('instance', 1)->orderBy('ordre')->get();
        foreach ($admin_fonctions as $fonction) {
            $utilisateur = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
                ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
                ->first();
            if ($utilisateur && $utilisateur->personne_id) {
                $fonction->utilisateur = $utilisateur;
            } else {
                $fonction->utilisateur = null;
            }
        }
        $ur_fonctions = Fonction::where('instance', 2)->where('urs_id', 0)->orderBy('id')->get();
        foreach ($ur_fonctions as $fonction) {
            $urs = Ur::join('fonctionsurs', 'fonctionsurs.urs_id', '=', 'urs.id')->where('fonctionsurs.fonctions_id', $fonction->id)->orderBy('urs.nom')->get();
            $fonction->urs = $urs;
        }
        return view('admin.fonctions.index', compact('admin_fonctions', 'ur_fonctions'));
    }

    public function ca()
    {
        $utilisateurs = Utilisateur::where('ca', 1)->get();
        return view('admin.fonctions.ca', compact('utilisateurs'));
    }

    public function ce()
    {
        $utilisateurs = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->where('fonctions.ce', 1)
            ->whereNotNull('utilisateurs.personne_id')
            ->orderBy('fonctions.ordre')
            ->selectRaw('utilisateurs.*, fonctions.libelle as libelle, fonctions.courriel as courriel')
            ->get();

        return view('admin.fonctions.ce', compact('utilisateurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fonction = new Fonction();
        return view('admin.fonctions.create', compact('fonction'));
    }

    public function create_ur()
    {
        $fonction = new Fonction();
        return view('admin.fonctions.create_ur', compact('fonction'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FonctionRequest $request)
    {
        // on regarde si la fonction existe déjà au niveau fédéral
        $exist_fonction = Fonction::where('libelle', trim($request->libelle))->where('instance', 1)->first();
        if ($exist_fonction) {
            return redirect()->back()->with('error', 'La fonction existe déjà')->withInput();
        }

        $data = $request->only('libelle', 'courriel');
        if (isset($request->ce)) {
            $data['ce'] = 1;
        }
        $data['instance'] = 1;
        $max_ordre = Fonction::where('instance', 1)->max('ordre');
        $data['ordre'] = $max_ordre + 1;
        $fonction = Fonction::create($data);

        // si l'identifiant utilisateur est renseigné, on l'ajoute à la table de liaison
        if ($request->identifiant != null) {
            $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
            if ($utilisateur) {
                DB::table('fonctionsutilisateurs')->insert([
                    'fonctions_id' => $fonction->id,
                    'utilisateurs_id' => $utilisateur->id,
                ]);
            }
        }
        return redirect()->route('fonctions.index')->with('success', 'La fonction a été créée');
    }

    public function store_ur(FonctionRequest $request)
    {
        $exist_fonction = Fonction::where('libelle', trim($request->libelle))->where('instance', 2)->first();
        if ($exist_fonction) {
            return redirect()->back()->with('error', 'La fonction existe déjà')->withInput();
        }
        $data = $request->only('libelle');
        $data['instance'] = 2;
        $max_ordre = Fonction::where('instance', 2)->max('ordre');
        $data['ordre'] = $max_ordre + 1;
        Fonction::create($data);

        return redirect()->route('fonctions.index')->with('success', 'La fonction a été créée');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fonction $fonction)
    {
        $utilisateur = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
            ->first();
        if ($utilisateur) {
            $fonction->utilisateur = $utilisateur;
        } else {
            $fonction->utilisateur = null;
        }
        return view('admin.fonctions.edit', compact('fonction'));
    }


    public function edit_ur(Fonction $fonction)
    {
        return view('admin.fonctions.edit_ur', compact('fonction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FonctionRequest $request, Fonction $fonction)
    {
        $data = $request->only('libelle', 'courriel');
        if (isset($request->ce)) {
            $data['ce'] = 1;
        } else {
            $data['ce'] = 0;
        }
        $fonction->update($data);

        // on regarde si un utilisateur est associé à la fonction
        $utilisateur_exitent = Utilisateur::join('fonctionsutilisateurs', 'utilisateurs.id', '=', 'fonctionsutilisateurs.utilisateurs_id')
            ->where('fonctionsutilisateurs.fonctions_id', $fonction->id)
            ->first();
        if ($utilisateur_exitent) {
            $identifiant_actuel = $utilisateur_exitent->identifiant;
            $utilisateur_actuel = $utilisateur_exitent->id;
        } else {
            $identifiant_actuel = null;
            $utilisateur_actuel = null;
        }
        if ($identifiant_actuel != $request->identifiant) {
            // si l'identifiant utilisateur est renseigné, on l'ajoute à la table de liaison
            if ($request->identifiant != null) {
                $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
                if ($utilisateur) {
                    DB::table('fonctionsutilisateurs')->insert([
                        'fonctions_id' => $fonction->id,
                        'utilisateurs_id' => $utilisateur->id,
                    ]);
                }
            }
            // on supprime l'ancien utilisateur pour la fonction associée
            if ($identifiant_actuel != null) {
                DB::table('fonctionsutilisateurs')
                    ->where('fonctions_id', $fonction->id)
                    ->where('utilisateurs_id', $utilisateur_actuel)
                    ->delete();
            }
        }
        return redirect()->route('fonctions.index')->with('success', 'La fonction a été modifiée');
    }

    public function update_ur(FonctionRequest $request, Fonction $fonction)
    {
        $data = $request->only('libelle');
        $fonction->update($data);
        return redirect()->route('fonctions.index')->with('success', 'La fonction a été modifiée');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fonction $fonction)
    {
        $fonction->delete();
        return redirect()->route('fonctions.index')->with('success', 'La fonction a été supprimée');
    }

    public function destroy_ca(Utilisateur $utilisateur) {
        $data = array('ca' => 0);
        $utilisateur->update($data);
        return redirect()->route('admin.fonctions.ca')->with('success', "L'adhérent a été supprimé du CA");
    }

    public function add_ca(Request $request) {
        $utilisateur = Utilisateur::where('identifiant', $request->identifiant)->first();
        if (!$utilisateur) {
            return redirect()->route('admin.fonctions.ca')->with('error', "L'adhérent n'existe pas");
        }
        if ($utilisateur->ca === 1) {
            return redirect()->route('admin.fonctions.ca')->with('error', "L'adhérent est déjà au CA");
        }
        $data = array('ca' => 1);
        $utilisateur->update($data);
        return redirect()->route('admin.fonctions.ca')->with('success', "L'adhérent a été ajouté au CA");
    }

    public function updateFonctionCe(Request $request)
    {
        $fonction = Fonction::where('id', $request->ref)->first();
        if (!$fonction) {
            return response()->json(['error' => 'Fonction non trouvée'], 404);
        }
        $data = array('ce' => $request->ce);
        $fonction->update($data);
        return response()->json(['success' => 'Fonction mise à jour'], 200);
    }
}
