<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorieformation;
use Illuminate\Http\Request;

class CategorieFormationController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorie = new Categorieformation();
        return view('admin.categorieformations.create', compact('categorie'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = ['name' => $request->name];
        Categorieformation::create($data);
        return redirect()->route('formations.parametrage')->with('success', 'La catégorie a bien été ajoutée.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($categorie_id)
    {
        $categorie = Categorieformation::where('id', $categorie_id)->first();
        return view('admin.categorieformations.edit', compact('categorie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $categorie_id)
    {
        $categorie = Categorieformation::where('id', $categorie_id)->first();
        $data = ['name' => $request->name];
        $categorie->update($data);
        return redirect()->route('formations.parametrage')->with('success', 'La catégorie a bien été modifiée.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($categorie_id)
    {
        $categorie = Categorieformation::where('id', $categorie_id)->first();
        $categorie->delete();
        return redirect()->route('formations.parametrage')->with('success', 'La catégorie a bien été supprimée.');
    }
}
