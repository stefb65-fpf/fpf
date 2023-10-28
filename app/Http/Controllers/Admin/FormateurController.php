<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormateurRequest;
use App\Models\Formateur;
use App\Models\Formation;
use App\Models\Personne;
use Illuminate\Http\Request;

class FormateurController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index() {
        $formateurs = Formateur::join('personnes', 'personnes.id', '=', 'formateurs.personne_id')
            ->select('formateurs.*', 'personnes.nom', 'personnes.prenom', 'personnes.email', 'personnes.phone_mobile')
            ->orderBy('personnes.nom')
            ->orderBy('personnes.prenom')
            ->get();

        return view('admin.formateurs.index', compact('formateurs'));
    }

    public function create() {
        $formateur = new Formateur();
        $formateur->personne = new Personne();
        return view('admin.formateurs.create', compact('formateur'));
    }

    public function store(FormateurRequest $request) {
        $email = trim($request->email);
        // on regarde si l'adresse email n'existe pas déjà dans la table personnes
        $personne = Personne::where('email', $email)->first();
        if ($personne) {
            return redirect()->back()->with('error', "L'adresse email saisie correspond déjà à une personne existante dans la base de données.")->withInput();
        }
        // on crée la personne
        $phone_mobile = $this->format_mobile_for_base($request->phone_mobile);
        if ($phone_mobile == -1) {
            return redirect()->back()->with('error', "Le numéro de téléphone mobile n'est pas valide")->withInput();
        }
        $datap = [
            'nom' => strtoupper($request->nom),
            'prenom' => $request->prenom,
            'email' => $email,
            'phone_mobile' => $phone_mobile,
            'is_formateur' => 1,
        ];
        $datap['password'] = $this->generateRandomPassword();
        $personne = Personne::create($datap);

        // on crée le formateur
        $dataf = $request->only('title', 'cv', 'website');
        $dataf['personne_id'] = $personne->id;
        Formateur::create($dataf);

        return redirect()->route('formateurs.index')->with('success', 'Le formateur a bien été ajouté.');
    }

    public function edit(Formateur $formateur) {
        return view('admin.formateurs.edit', compact('formateur'));
    }

    public function update(FormateurRequest $request, Formateur $formateur) {
        $datap = $request->only('nom', 'prenom');
        $phone_mobile = $this->format_mobile_for_base($request->phone_mobile);
        if ($phone_mobile == -1) {
            return redirect()->back()->with('error', "Le numéro de téléphone mobile n'est pas valide")->withInput();
        }
        $datap['phone_mobile'] = $phone_mobile;
        $personne = $formateur->personne;
        $personne->update($datap);

        $dataf = $request->only('title', 'cv', 'website');
        $formateur->update($dataf);

        return redirect()->route('formateurs.index')->with('success', 'Le formateur a bien été modifié.');
    }

    public function destroy(Formateur $formateur) {
        $personne = $formateur->personne;
        $data = ['is_formateur' => 0];
        $personne->update($data);

        $formateur->delete();
        return redirect()->route('formateurs.index')->with('success', 'Le formateur a bien été supprimé.');
    }

    public function liste(Formation $formation) {
        $selected_formateurs = $formation->formateurs()->pluck('formateurs.id')->toArray();
        $formateurs = Formateur::join('personnes', 'personnes.id', '=', 'formateurs.personne_id')
            ->select('formateurs.id', 'personnes.nom', 'personnes.prenom')
            ->orderBy('personnes.nom')
            ->orderBy('personnes.prenom')
            ->get();
        foreach ($formateurs as $k => $formateur) {
            if (in_array($formateur->id, $selected_formateurs)) {
                unset($formateurs[$k]);
            }
        }
        return view('admin.formateurs.liste', compact('formation', 'formateurs'));
    }

    public function add(Request $request, Formation $formation) {
        $formation->formateurs()->attach($request->formateur_id);
        return redirect()->back()->with('success', 'Le formateur a bien été à la formation.');
    }

    public function remove(Formation $formation, Formateur $formateur) {
        $formation->formateurs()->detach($formateur->id);
        return redirect()->back()->with('success', 'Le formateur a bien été retiré de la formation.');
    }
}
