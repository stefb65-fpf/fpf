<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormationRequest;
use App\Models\Categorieformation;
use App\Models\Evaluationstheme;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function accueil() {
        return view('admin.formations.accueil');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formations = Formation::orderByDesc('created_at')->get();
        return view('admin.formations.index', compact('formations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formation = new Formation();
        $categories = Categorieformation::orderBy('id')->get();
        return view('admin.formations.create', compact('formation', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormationRequest $request)
    {
        $data = $request->only('name', 'shortDesc', 'longDesc', 'program', 'categories_formation_id', 'type', 'location', 'level', 'price',
            'places', 'waiting_places', 'duration');
        if (isset($request->new)) {
            $data['new'] = 1;
        }
        if ($data['waiting_places'] == null) {
            $data['waiting_places'] = 0;
        }
        Formation::create($data);
        return redirect()->route('formations.index')->with('success', 'Formation créée avec succès');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formation $formation)
    {
        $categories = Categorieformation::orderBy('id')->get();
        return view('admin.formations.edit', compact('formation', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formation $formation)
    {
        $data = $request->only('name', 'shortDesc', 'longDesc', 'program', 'categories_formation_id', 'type', 'location', 'level', 'price',
            'places', 'waiting_places', 'duration');
        $data['new'] = isset($request->new) ? 1 : 0;
        if ($data['waiting_places'] == null) {
            $data['waiting_places'] = 0;
        }
        $formation->update($data);
        return redirect()->route('formations.index')->with('success', 'Formation modifiée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        $formation->delete();
        return redirect()->route('formations.index')->with('success', 'Formation supprimée avec succès');
    }

    public function activate(Formation $formation) {
        $data = array('published' => 1);
        $formation->update($data);
        return redirect()->route('formations.index')->with('success', 'Formation activée avec succès');
    }

    public function deactivate(Formation $formation) {
        $data = array('published' => 0);
        $formation->update($data);
        return redirect()->route('formations.index')->with('success', 'Formation désactivée avec succès');
    }

    public function parametrage() {
        $categories = Categorieformation::orderBy('id')->get();
        $evalthemes = Evaluationstheme::orderBy('position')->get();
//        foreach ($evalthemes as $evaltheme) {
//            dd($evaltheme->evaluationsitems->sortBy('position'));
//        }

        return view('admin.formations.parametrage', compact('categories', 'evalthemes'));
    }
}
