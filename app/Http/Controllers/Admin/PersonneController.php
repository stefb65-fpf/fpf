<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class PersonneController extends Controller
{
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

    public function listeUtilisateurs($view_type, $statut = null, $type_carte = null, $type_adherent = null, $term = null)
    {
        $limit_pagination=100;
        $query = Utilisateur::where('urs_id' ,'!=',null)->where('personne_id' ,'!=', null);
//dd($view_type);
        $utilisateurs = $query->paginate($limit_pagination);
        return view('admin.personnes.liste_personnes_template', compact('view_type', 'utilisateurs', 'statut', 'type_carte', 'type_adherent', 'term'));
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
