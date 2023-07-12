<?php

namespace App\Http\Controllers;

use App\Concern\Tools;
use App\Models\Club;
use App\Models\Ur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'urAccess']);
    }

    public function gestion() {
        $ur = $this->getUr();
        return view('urs.gestion', compact('ur'));
    }

    public function infosUr() {
        $ur = $this->getUr();
        return view('urs.infos_ur', compact('ur'));
    }

    public function listeClubs($statut = null, $type_carte = null, $abonnement = null) {
        $ur = $this->getUr();
//        dd($ur);
        $clubs = Club::where('urs_id', $ur->id)->orderBy('numero')->paginate(100);
        if(!$statut){
            $statut = "all";
        }
        if(!$abonnement){
            $abonnement = "all";
        }

        if (($statut != null) && ($statut != 'all')) {
            //verifier que le parametre envoyé existe
            $lestatut = in_array($statut,[0,1,2,3]);
            if ($lestatut) {
                $clubs  = $clubs->where('statut', $statut);
            }
        }
//        dd($clubs);
        foreach ($clubs as $club) {
            // on récupère le contact
            $contact = DB::table('fonctionsutilisateurs')->join('utilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->join('personnes', 'personnes.id', '=', 'utilisateurs.personne_id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $club->id)
                ->selectRaw('utilisateurs.id, utilisateurs.identifiant, personnes.nom, personnes.prenom')
                ->first();
            $club->contact = $contact ?? null;
            $club->numero = $this->complement_string_to($club->numero, 4);
//            $club->urs_id = $this->complement_string_to($club->urs_id , 2);
//            dd($club);
            $club->adresse->callable_mobile = $this->format_phone_number_callable($club->adresse->telephonemobile);
            $club->adresse->visual_mobile = $this->format_phone_number_visual($club->adresse->telephonemobile);    $club->adresse->callable_fixe = $this->format_phone_number_callable($club->adresse->telephonedomicile);
            $club->adresse->visual_fixe = $this->format_phone_number_visual($club->adresse->telephonedomicile);
            //changer les url des adresses web
        }
        return view('urs.liste_clubs', compact('ur',"statut","type_carte","abonnement"));
    }

    public function listeAdherents() {
        $ur = $this->getUr();
        return view('urs.liste_adherents', compact('ur'));
    }

    public function listeFonctions() {
        $ur = $this->getUr();
        return view('urs.liste_fonctions', compact('ur'));
    }

    public function listeReversements() {
        $ur = $this->getUr();
        return view('urs.liste_reversements', compact('ur'));
    }

    protected function getUr() {
        $cartes = session()->get('cartes');
        if (!$cartes || count($cartes) == 0) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
        }
        $active_carte = $cartes[0];
        $ur_id = $active_carte->urs_id;
        $ur = Ur::where('id', $ur_id)->first();
        if (!$ur) {
            return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
        }
        return $ur;
    }

}
