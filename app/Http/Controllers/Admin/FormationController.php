<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Exports\FormationsListeExport;
use App\Exports\RecapFormations;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormationRequest;
use App\Models\Categorieformation;
use App\Models\Club;
use App\Models\Evaluationsitem;
use App\Models\Evaluationstheme;
use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Inscrit;
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
    public function index(Request $request)
    {
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
//        $formations = Formation::orderByDesc('created_at')->get();
        $formations = Formation::query()
            ->with(['formateurs'])
            ->when($request->filled('type') || $request->type === '0', function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->categorie, function ($query, $categorie) {
                $query->where('categories_formation_id', $categorie);
            })
            ->when($request->formateur, function ($query, $formateurId) {
                $query->whereHas('formateurs', function ($q) use ($formateurId) {
                    $q->where('formateur_id', $formateurId);
                });
            })
            ->when($request->new !== null && $request->new !== '', function ($query) use ($request) {
                $query->where('new', $request->new);
            })
            ->where('archived', false)
            ->orderByDesc('created_at')
            ->get();
        $tab_formateurs = [];
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
            foreach ($formation->formateurs as $formateur) {
                if (!isset($tab_formateurs[$formateur->personne->nom])) {
                    $tab_formateurs[$formateur->personne->nom] = $formateur;
                }
            }
        }
        ksort($tab_formateurs);
        $formations = $formations->sort(function($a, $b) {
            return $a->trinom > $b->trinom;
        });

        $types = [0 => 'distanciel', 1 => 'présentiel', 2 => 'les deux'];
        $categories = CategorieFormation::all();
        $formateurs = Formateur::all();
        return view('admin.formations.index', compact('formations', 'types', 'categories', 'formateurs', 'tab_formateurs'));
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
        $formation->global_price = 0;
        $categories = Categorieformation::orderBy('id')->get();
        return view('admin.formations.create', compact('formation', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormationRequest $request)
    {
        $data = $request->only('name', 'shortDesc', 'longDesc', 'program', 'categories_formation_id', 'type', 'location', 'level', 'price', 'price_not_member',
            'places', 'waiting_places', 'duration', 'global_price');
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
            'places', 'waiting_places', 'duration', 'global_price');
        $data['new'] = isset($request->new) ? 1 : 0;
        if ($data['waiting_places'] == null) {
            $data['waiting_places'] = 0;
        }
        $formation->update($data);
        // on ajoute #formation->id à l'url de rediorection pour retomber au bon endroit
        $url = route('formations.index') . '#' . $formation->id;
        return redirect($url)->with('success', 'Formation modifiée avec succès');
//        return redirect()-->with('success', 'Formation modifiée avec succès');
    }


    public function archive(Formation $formation)
    {
        $formation->update(['archived' => true, 'published' => false]);
        return redirect()->route('formations.index')->with('success', 'Formation archivée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        $formation->delete();
        return redirect()->route('formations.index')->with('success', 'Formation supprimée avec succès');
    }

    public function delete_dashboard(Formation $formation)
    {
        $formation->delete();
        return redirect()->route('formations.dashboard')->with('success', 'Formation supprimée avec succès');
    }

    public function activate(Formation $formation) {
        if (count($formation->formateurs) == 0) {
            return redirect()->route('formations.index')->with('error', 'Pour publier la formation, vous devez saisir au moins un formateur.');
        }
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

    public function exportListe() {
//        $formations = Formation::orderBy('id')->get();
        $formations = Formation::with(['sessions' => function ($query) {
            $query->orderBy('start_date');
        }])->orderBy('id')->get();
        $fichier = 'liste_formations_' . date('YmdHis') . '.xls';
        if (Excel::store(new FormationsListeExport($formations), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            $texte = "Vous pouvez télécharger le fichier en cliquant sur le lien suivant : <a href='" . $file_to_download . "'>Télécharger</a>";
            return redirect()->route('formations.index')->with('success', $texte);
        } else {
            return redirect()->route('formations.index')->with('success', "Un problème est survenu lors de l'export");
        }
    }

    public function dashboard() {
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
        $formations = Formation::orderByDesc('created_at')->where('published', 1)->get();
        foreach ($formations as $formation) {
            foreach ($formation->sessions as $session) {
                $session->numero_club = '';
                $session->nom_club = '';
                if ($session->club_id != null) {
                    $club = Club::where('id', $session->club_id)->first();
                    if ($club) {
                        $session->numero_club = $club->numero;
                        $session->nom_club = $club->nom;
                    }
                }
                $session->inscrits = Inscrit::where('session_id', $session->id)->where('status', 1)->count();
                $session->inscrits_attente = Inscrit::where('session_id', $session->id)->where('status', 0)->count();
            }
        }
        return view('admin.formations.dashboard', compact('formations'));
    }
}
