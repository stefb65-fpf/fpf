<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonneRequest;
use App\Mail\SendAnonymisationEmail;
use App\Mail\SendInvoice;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PersonneController extends Controller
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
        return view('admin.personnes.index');
    }

    public function listeAdherents()
    {

        return view('admin.liste_adherents');
    }

    public function list($view_type,  $ur_id = null,$statut = null,$type_carte = null, $type_adherent = null, $term = null)
    {
        $statut = $statut ?? "all";
        $type_adherent = $type_adherent ?? "all";
        $ur = null;
        if($view_type == "formateurs"){
            //TODO : on affiche les formateurs
            $query = Personne::where('is_adherent',0)->where('is_formateur' ,'!=', 0);
        } elseif ($view_type == "abonnes"){
            $query = Personne::where('is_adherent',0)->where('is_abonne' ,'!=', 0);
//        } elseif($view_type == "ur_adherents"){
//            $cartes = session()->get('cartes');
//            if (!$cartes || count($cartes) == 0) {
//                return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
//            }
//            $active_carte = $cartes[0];
//            $ur = Ur::where('id', $active_carte->urs_id)->first();
//            if (!$ur || ($active_carte->urs_id != $ur_id)) {
//                return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
//            }
//            $query = Utilisateur::where('urs_id', '=', $ur->id)->where('personne_id' ,'!=', null);
        } elseif($view_type == "recherche") {
            $query = Personne::join('utilisateurs', 'utilisateurs.personne_id','=','personnes.id' );
            if($term){
                //appel de la fonction getPersonsByTerm($club, $term) qui retourne les personnes filtrées selon le term
                $this->getPersonsByTerm($term, $query);
            }
        } else {
            $query = Utilisateur::where('urs_id' ,'!=', null)->where('urs_id' ,'!=', 0)->where('personne_id' ,'!=', null);
        }

        if ($ur_id != 'all' && $ur_id) {
            $lur = Ur::where('id',$ur_id)->first();
            if ($lur) {
                $query  = $query->where('utilisateurs.urs_id','=', $lur->id);
            }
        }

        if ($statut != 'all' && in_array($statut, [0,1,2,3,4])) {
            $query  = $query->where('statut', $statut);
        }
        if ($type_adherent != 'all' && in_array($type_adherent,[1,2])) {
            if($type_adherent == 1) {
                $query  = $query->whereNull('clubs_id');
//                $query  = $query->where('clubs_id', null);
            } else {
//            }elseif($type_adherent == 2){
                $query  = $query->whereNotNull('clubs_id');
//                $query  = $query->where('clubs_id','!=', null);
            }
        }
        if ($type_carte != 'all' && (in_array($type_carte,[2,3,4,5,6,7,8,9,"F"]))) {
            $query  = $query->where('ct', $type_carte);
        }
        $utilisateurs = $query->paginate(100);
        $urs = Ur::orderBy('nom')->get();
        $level = 'admin';
        return view('admin.personnes.liste', compact('view_type', 'utilisateurs', 'statut', 'type_carte', 'level', 'type_adherent','ur_id','urs','ur', 'term'));
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
     * Show the form for editing the specified resource.
     */
    public function edit($personne_id, $view_type) {

        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return redirect('/admin/personnes/'.$view_type)->with('error', "Un problème est survenu lors de la récupération des informations de la personne");
        }
        if (sizeof($personne->adresses) == 0) {
            return redirect('/admin/personnes/'.$view_type)->with('error', "Un problème est survenu lors de la récupération des adresses de la personne");
        }
        foreach ($personne->adresses as $adresse) {
            $pays = Pays::where('nom', $adresse->pays)->first();
            if ($pays) {
                $adresse->indicatif = $pays->indicatif;
            }
        }
        $countries = DB::table('pays')->orderBy('nom')->get();
        $level = 'admin';
        return view('admin.personnes.edit', compact('personne', 'view_type', 'countries', 'level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonneRequest $request, Personne $personne, $view_type)
    {
        $datap = $request->only('nom', 'prenom', 'datenaissance', 'email', 'phone_mobile', 'sexe');
        $personne->update($datap);

        $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville', 'telephonedomicile');
        $pays = Pays::where('id', $request->pays)->first();
        if ($pays) {
            $dataa['pays'] = $pays->nom;
        }
        $adresse_1 = $personne->adresses[0];
        $adresse_1->update($dataa);

        if (isset($request->villeLivraison) && isset($request->codepostalLivraison) && isset($request->paysLivraison) && isset($personne->adresses[1])) {
            $dataa2 = $request->only('libelle1Livraison', 'libelle2Livraison', 'codepostalLivraison', 'villeLivraison', 'telephonedomicileLivraison');
            $pays = Pays::where('id', $request->paysLivraison)->first();
            if ($pays) {
                $dataa2['pays'] = $pays->nom;
            }
            $adresse_2 = $personne->adresses[1];
            $adresse_2->update($dataa2);
        }
        return redirect('/admin/personnes/'.$view_type)->with('success', "La personne a bien été mise à jour");
    }

    public function anonymize(Personne $personne, $view_type)
    {
        try {
            DB::beginTransaction();
            $prev_email = $personne->email;
            $email = uniqid('anonyme_').'@federationphoto.fr';
            $datau = array('nom' => 'anonyme', 'prenom' => 'anonyme', 'courriel' => $email);
            foreach ($personne->utilisateurs as $utilisateur) {
                $utilisateur->update($datau);
            }

            $data = array('nom' => 'anonyme', 'prenom' => 'anonyme', 'email' => $email, 'phone_mobile' => '', 'news' => 0, 'is_adherent' => 0, 'is_abonne' => 0,
                'is_formateur' => 0, 'is_administratif' => 0);
            $personne->update($data);

            $mailSent = Mail::to($prev_email)->send(new SendAnonymisationEmail());
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

            if ($personne) {
                $mail = new \stdClass();
                $mail->titre = "Anonymisation des données personnelles";
                $mail->destinataire = $email;
                $mail->contenu = $htmlContent;
                $this->registerMail($personne->id, $mail);
            }
            DB::commit();

            return redirect('/admin/personnes/'.$view_type)->with('success', "La personne a été anonymisée avec succès");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/admin/personnes/'.$view_type)->with('error', "Un problème est survenu lors de l'anonymisation de la personne");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
