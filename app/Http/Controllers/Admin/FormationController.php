<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Exports\RecapFormations;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormationRequest;
use App\Models\Categorieformation;
use App\Models\Evaluationsitem;
use App\Models\Evaluationstheme;
use App\Models\Formation;
use App\Models\Interest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FormationController extends Controller
{
    use Tools;
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
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
        $formations = Formation::orderByDesc('created_at')->get();
        foreach($formations as $formation) {
            $formation->trinom = isset($formation->formateurs[0]) ? $formation->formateurs[0]->personne->nom : '';
            $formation->interests = Interest::where('formation_id', $formation->id)->count();
            $exist_eval = 0;
            foreach($formation->sessions as $session) {
                if ($session->evaluations->count() > 0) {
                    $exist_eval = 1;
                }
            }
            $formation->exist_eval = $exist_eval;
        }
        $formations = $formations->sort(function($a, $b) {
            return $a->trinom > $b->trinom;
        });
        return view('admin.formations.index', compact('formations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
        $formation = new Formation();
        $categories = Categorieformation::orderBy('id')->get();
        return view('admin.formations.create', compact('formation', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormationRequest $request)
    {
        $data = $request->only('name', 'shortDesc', 'longDesc', 'program', 'categories_formation_id', 'type', 'location', 'level', 'price', 'price_not_member',
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
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
        $categories = Categorieformation::orderBy('id')->get();
        return view('admin.formations.edit', compact('formation', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formation $formation)
    {
        $data = $request->only('name', 'shortDesc', 'longDesc', 'program', 'categories_formation_id', 'type', 'location', 'level', 'price', 'price_not_member',
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
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
        $categories = Categorieformation::orderBy('id')->get();
        $evalthemes = Evaluationstheme::orderBy('position')->get();
//        foreach ($evalthemes as $evaltheme) {
//            dd($evaltheme->evaluationsitems->sortBy('position'));
//        }

        return view('admin.formations.parametrage', compact('categories', 'evalthemes'));
    }

    public function evaluations(Formation $formation) {
        $tab_evaluations = array();
        $tab_reviews = array();
        foreach ($formation->sessions as $session) {
            foreach ($session->evaluations as $evaluation) {
                if ($evaluation->comment != '') {
                    $tab_reviews[] = $evaluation->comment;
                } else {
                    if (!isset($tab_evaluations[$evaluation->evaluationsitem_id])) {
                        // on cherche l'item
                        $item = Evaluationsitem::where('id', $evaluation->evaluationsitem_id)->first();
//                        dd($item->evaluationstheme->name);
                        if ($item) {
                            $tab_evaluations[$evaluation->evaluationsitem_id]['name'] = $item->evaluationstheme->name.' - '.$item->name;
                        }
                        $tab_evaluations[$evaluation->evaluationsitem_id]['note'] = $evaluation->stars;
                        $tab_evaluations[$evaluation->evaluationsitem_id]['nb'] = 1;
                    } else {
                        $total = $tab_evaluations[$evaluation->evaluationsitem_id]['note'] * $tab_evaluations[$evaluation->evaluationsitem_id]['nb'];
                        $total += $evaluation->stars;
                        $tab_evaluations[$evaluation->evaluationsitem_id]['note'] = round($total / ($tab_evaluations[$evaluation->evaluationsitem_id]['nb'] + 1), 1);
                        $tab_evaluations[$evaluation->evaluationsitem_id]['nb'] = $tab_evaluations[$evaluation->evaluationsitem_id]['nb'] + 1;
                    }
                }
            }
        }
//        foreach ($tab_evaluations as $evaluation) {
//            dd($evaluation);
//        }
        return view('admin.formations.evaluations', compact('formation', 'tab_evaluations', 'tab_reviews'));
    }

    public function export() {
        $formations = Formation::orderBy('id')->get();
        foreach ($formations as $j => $formation) {
            foreach ($formation->sessions as $k => $session) {
                if ($session->start_date < date('Y-m-d')) {
                    unset($formation->sessions[$k]);
                }
            }
            if (sizeof($formation->sessions) == 0) {
                unset($formations[$j]);
            }
        }
        $fichier = 'recap_formation' . date('YmdHis') . '.xls';
        if (Excel::store(new RecapFormations($formations), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            $texte = "Vous pouvez télécharger le fichier en cliquant sur le lien suivant : <a href='" . $file_to_download . "'>Télécharger</a>";
            return redirect()->route('formations.index')->with('success', $texte);
        } else {
            return redirect()->route('formations.index')->with('success', "Un problème est survenu lors de l'export");
        }
    }
}
