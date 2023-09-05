<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonneRequest;
use App\Mail\SendAnonymisationEmail;
use App\Mail\SendUtilisateurCreateByAdmin;
use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Configsaison;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Ur;
use App\Models\Utilisateur;
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

    public function list($view_type, $ur_id = null, $statut = null, $type_carte = null, $type_adherent = null, $term = null)
    {
        $statut = $statut ?? "all";
        $type_adherent = $type_adherent ?? "all";
        $ur = null;
        if ($view_type == "formateurs") {
            //TODO : on affiche les formateurs
            $query = Personne::where('is_adherent', 0)->where('is_formateur', '!=', 0);
        } elseif ($view_type == "abonnes") {
            $query = Personne::where('is_adherent', 0)->where('is_abonne', '!=', 0)->orderBy('nom')->orderBy('prenom');
        } elseif ($view_type == "recherche") {
            $query = Personne::join('utilisateurs', 'utilisateurs.personne_id', '=', 'personnes.id');
            if ($term) {
                //appel de la fonction getPersonsByTerm($club, $term) qui retourne les personnes filtrées selon le term
                $this->getPersonsByTerm($term, $query);
            }
        } else {
            $query = Utilisateur::where('urs_id', '!=', null)->where('urs_id', '!=', 0)->where('personne_id', '!=', null)
                ->orderBy('urs_id')->orderBy('clubs_id')->orderBy('nom')->orderBy('prenom');
        }

        if ($ur_id != 'all' && $ur_id) {
            $lur = Ur::where('id', $ur_id)->first();
            if ($lur) {
                $query = $query->where('utilisateurs.urs_id', '=', $lur->id);
            }
        }

        if ($statut != 'all' && in_array($statut, [0, 1, 2, 3, 4])) {
            $query = $query->where('statut', $statut);
        }
        if ($type_adherent != 'all' && in_array($type_adherent, [1, 2])) {
            if ($type_adherent == 1) {
                $query = $query->whereNull('clubs_id');
            } else {
                $query = $query->whereNotNull('clubs_id');
            }
        }
        if ($type_carte != 'all' && (in_array($type_carte, [2, 3, 4, 5, 6, 7, 8, 9, "F"]))) {
            $query = $query->where('ct', $type_carte);
        }
        $utilisateurs = $query->paginate(100);
        if($view_type == "recherche") {
            foreach ($utilisateurs as $utilisateur) {
                if ($utilisateur->personne_id) {
                    $personne = Personne::where('id', $utilisateur->personne_id)->first();
                    $utilisateur->personne = $personne;
                }
            }
        }
//        dd($utilisateurs);
        $urs = Ur::orderBy('nom')->get();
        $level = 'admin';

        foreach ($utilisateurs as $utilisateur) {
            $fin = '';
            $is_abonne = 0;
//            dd($utilisateur->personne);
            if (in_array($view_type, ['abonnes', 'formateurs'])) {
                $is_abonne = $utilisateur->is_abonne;
                $personne_id = $utilisateur->id;
            } else {
                $is_abonne = $utilisateur->personne->is_abonne;
                $personne_id = $utilisateur->personne->id;
            }
            if ($is_abonne) {
                $personne_abonnement = Abonnement::where('personne_id', $personne_id)->where('etat', 1)->first();
                if ($personne_abonnement) {
                    $fin = $personne_abonnement->fin;
                }
            }
            $utilisateur->fin = $fin;
        }
        return view('admin.personnes.liste', compact('view_type', 'utilisateurs', 'statut', 'type_carte', 'level', 'type_adherent', 'ur_id', 'urs', 'ur', 'term'));
    }

//    public function listeAbonnes()
//    {
//        return view('admin.personnes.liste_abonnes');
//    }
//
//    public function listeFormateurs()
//    {
//        return view('admin.personnes.liste_formateurs');
//    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($personne_id, $view_type)
    {

        $personne = Personne::where('id', $personne_id)->first();
        if (!$personne) {
            return redirect('/admin/personnes/' . $view_type)->with('error', "Un problème est survenu lors de la récupération des informations de la personne");
        }
        if (sizeof($personne->adresses) == 0) {
            $new_adresse = new Adresse();
            $new_adresse->pays = 'France';
            $personne->adresses[] = $new_adresse;
        }
        $personne->fin = '';
        if ($personne->is_abonne) {
            $abonnement = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $personne->fin = $abonnement->fin;
            }
        }
//        if (sizeof($personne->adresses) == 0) {
//            return redirect('/admin/personnes/'.$view_type)->with('error', "Un problème est survenu lors de la récupération des adresses de la personne");
//        }

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

    public function create($view_type)
    {
        $countries = DB::table('pays')->orderBy('nom')->get();
        $personne = new Personne();
        $personne->adresses[] = new Adresse();
        $personne->adresses[0]->pays = 'France';
        $personne->adresses[0]->indicatif = '33';
        $level = 'admin';
        return view('admin.personnes.create', compact('view_type', 'countries', 'level', 'personne'));
    }

    public function store(PersonneRequest $request, $view_type)
    {
        $email = trim($request->email);
        list($tmp, $domain) = explode('@', $email);
        if ($domain == 'federation-photo.fr') {
            return redirect()->back()->with('error', "Vous ne pouvez pas indiquer une adresse email contenant le domaine federation-photo.fr")->withInput();
        }
        $olduser = Personne::where('email', $email)->first();
        if ($olduser) {
            return redirect()->back()->with('error', "Une personne possédant la même adresse email existe déjà")->withInput();
        }

        $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville');
        $datap = $request->only('nom', 'prenom', 'datenaissance', 'sexe');
        if ($request->news) {
            $datap['news'] = 1;
        } else {
            $datap['news'] = 0;
        }
        $pays = Pays::where('id', $request->pays)->first();
        if ($pays) {
            $dataa['pays'] = $pays->nom;
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
            $telephonedomicile = $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif);
        } else {
            $dataa['pays'] = 'France';
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile);
            $telephonedomicile = $this->format_fixe_for_base($request->telephonedomicile);
        }
        if ($telephonedomicile == -1) {
            return redirect()->back()->with('error', "Le numéro de téléphone fixe n'est pas valide")->withInput();
        }
        $dataa['telephonedomicile'] = $telephonedomicile;
        if ($phone_mobile == -1) {
            return redirect()->back()->with('error', "Le numéro de téléphone mobile n'est pas valide")->withInput();
        }
        $datap['phone_mobile'] = $phone_mobile;

        $datap['email'] = $request->email;
        $datap['password'] = $this->generateRandomPassword();
        $personne = Personne::create($datap);

        $addresse = Adresse::create($dataa);

        // on lie l'adresse à la personne
        $personne->adresses()->attach($addresse->id);

        if ($view_type == 'adherents') {
            $datap2 = array('is_adherent' => 1);
            $personne->update($datap2);

            // on cherche l'ur pour le nouvel utilisateur
            list($identifiant, $urs_id, $numero) = $this->setIdentifiant($personne->adresses[0]->codepostal);
            list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($personne->datenaissance);
            $datau = [
                'urs_id' => $urs_id,
                'personne_id' => $personne->id,
                'identifiant' => $identifiant,
                'numeroutilisateur' => $numero,
                'sexe' => $personne->sexe,
                'nom' => $personne->nom,
                'prenom' => $personne->prenom,
                'ct' => $ct,
                'statut' => 0,
                'saison' => date('Y') - 1,
            ];
            $utilisateur = Utilisateur::create($datau);

            // on envoie le mail d'information a l'utilisateur
            $mailSent = Mail::to($personne->email)->send(new SendUtilisateurCreateByAdmin($personne->email));
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

            $mail = new \stdClass();
            $mail->titre = "Création d'un compte adhérent";
            $mail->destinataire = $email;
            $mail->contenu = $htmlContent;
            $this->registerMail($personne->id, $mail);

            return redirect('/admin/personnes/' . $view_type)->with('success', "L'adhérent $utilisateur->identifiant a bien été créé et un email d'information lui a été transmis à l'adresse $email");
        }

        if ($view_type == 'abonnes') {
            // on vcréée l'abonnement de la personne
            $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
            $numeroencours = $config->numeroencours;
            if ($request->fin != '') {
                $fin = $request->fin;
            } else {
                $fin = $numeroencours + 5;
            }

            $abonnement = Abonnement::create(['personne_id' => $personne->id, 'debut' => $numeroencours, 'fin' => $fin, 'etat' => 1]);
            if ($abonnement) {
                $personne->update(['is_abonne' => 1]);
            }


            return redirect('/admin/personnes/' . $view_type)->with('success', "L'abonné a bien été créé");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonneRequest $request, Personne $personne, $view_type)
    {
        $datap = $request->only('nom', 'prenom', 'datenaissance', 'sexe');
        if ($request->news) {
            $datap['news'] = 1;
        } else {
            $datap['news'] = 0;
        }
        $dataa = $request->only('libelle1', 'libelle2', 'codepostal', 'ville');
        $pays = Pays::where('id', $request->pays)->first();
        if ($pays) {
            $dataa['pays'] = $pays->nom;
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
            $telephonedomicile = $this->format_fixe_for_base($request->telephonedomicile, $pays->indicatif);
        } else {
            $dataa['pays'] = 'France';
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile);
            $telephonedomicile = $this->format_fixe_for_base($request->telephonedomicile);
        }
        if ($telephonedomicile == -1) {
            return redirect()->back()->with('error', "Le numéro de téléphone fixe n'est pas valide")->withInput();
        }
        $dataa['telephonedomicile'] = $telephonedomicile;
        if ($phone_mobile == -1) {
            return redirect()->back()->with('error', "Le numéro de téléphone mobile n'est pas valide")->withInput();
        }
        $datap['phone_mobile'] = $phone_mobile;
        if ($request->email !== $personne->email) {
            list($tmp, $domain) = explode('@', $request->email);
            if ($domain == 'federation-photo.fr') {
                return redirect()->back()->with('error', "Vous ne pouvez pas indiquer une adresse email contenant le domaine federation-photo.fr")->withInput();
            }
            // on regarde si aucun utilisateur n'existe avec le mail saisi
            $olduser = Personne::where('email', $request->email)->first();
            if ($olduser) {
                return redirect()->back()->with('error', "Une personne possédant la même adresse email existe déjà")->withInput();
            }
            $datap['email'] = $request->email;
        }
        $personne->update($datap);
        if ($personne->utilisateurs) {
            foreach ($personne->utilisateurs as $utilisateur) {
                $datau = array('nom' => $request->nom, 'prenom' => $request->prenom, 'courriel' => $request->email);
                $utilisateur->update($datau);
            }
        }

        $adresse_1 = $personne->adresses[0];
        $adresse_1->update($dataa);

        if (isset($request->villeLivraison) && isset($request->codepostalLivraison) && isset($request->paysLivraison) && isset($personne->adresses[1])) {
            $dataa2 = $request->only('libelle1Livraison', 'libelle2Livraison', 'codepostalLivraison', 'villeLivraison');
            $pays = Pays::where('id', $request->paysLivraison)->first();
            if ($pays) {
                $dataa2['pays'] = $pays->nom;
                $dataa2['telephonedomicileLivraison'] = $this->format_fixe_for_base($request->telephonedomicileLivraison, $pays->indicatif);
            } else {
                $dataa2['pays'] = 'France';
                $dataa2['telephonedomicileLivraison'] = $this->format_fixe_for_base($request->telephonedomicileLivraison);
            }
            $adresse_2 = $personne->adresses[1];
            $adresse_2->update($dataa2);
        }

        if($view_type == 'abonnes') {
            $abonnement = Abonnement::where('personne_id', $personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $abonnement->update(['fin' => $request->fin]);
            }
        }
        return redirect('/admin/personnes/' . $view_type)->with('success', "La personne a bien été mise à jour");
    }

    public function anonymize(Personne $personne, $view_type)
    {
        try {
            DB::beginTransaction();
            $prev_email = $personne->email;
            $email = uniqid('anonyme_') . '@federationphoto.fr';
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

            return redirect('/admin/personnes/' . $view_type)->with('success', "La personne a été anonymisée avec succès");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/admin/personnes/' . $view_type)->with('error', "Un problème est survenu lors de l'anonymisation de la personne");
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
