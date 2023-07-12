<?php

namespace App\Http\Controllers;

use App\Concern\ClubTools;
use App\Concern\Tools;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\ClubReunionRequest;
use App\Models\Club;
use App\Models\Ur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrController extends Controller
{
    use Tools;
    use ClubTools;
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

        if($statut === null){
            $statut = "all";
        }
        if( $abonnement === null){
            $abonnement = "all";
        }
        //TODO: régler le problème de rendre du paginate (la methode render() du blade réponds en erreur si la limite de pagination est supérieur au nombre de clubs dans $clubs!
        $limit_pagination = 100;
        $clubs = Club::where('urs_id', $ur->id)->orderBy('numero')->paginate($limit_pagination);
//dd($clubs);
        if (($statut != null) && ($statut != 'all')) {
            //verifier que le parametre envoyé existe
            $lestatut = in_array(strval($statut),["0","1","2","3"]);
            if ($lestatut) {
                $clubs  = $clubs->where('statut', $statut);
            }
        }
        if (($abonnement != null) && ($abonnement != 'all')) {
            //verifier que le parametre envoyé existe
            $labonnement = in_array(strval($abonnement),["0","1","G"]);

            if ($labonnement) {
                $clubs  = $clubs->where('abon', $abonnement);
            }
        }

        if (($type_carte != null) && ($type_carte != 'all')) {
            //verifier que le parametre envoyé existe
            $letypecarte = in_array(strval($type_carte),["1","N","C","A"]);
            if ($letypecarte) {
                $clubs  = $clubs->where('ct', $type_carte);
            }
        }

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
            //TODO:remove this
            $club->urs_id = $this->complement_string_to($club->urs_id , 2);
//            dd($club);
            $club->adresse->callable_mobile = $this->format_phone_number_callable($club->adresse->telephonemobile);
            $club->adresse->visual_mobile = $this->format_phone_number_visual($club->adresse->telephonemobile);    $club->adresse->callable_fixe = $this->format_phone_number_callable($club->adresse->telephonedomicile);
            $club->adresse->visual_fixe = $this->format_phone_number_visual($club->adresse->telephonedomicile);
            //changer les url des adresses web
        }
//        dd($clubs);

        return view('urs.liste_clubs', compact('ur','clubs','statut','type_carte','abonnement','limit_pagination'));
    }

    public function listeAdherents() {
        $ur = $this->getUr();
        return view('urs.liste_adherents', compact('ur'));
    }
    public function listeAdherentsClub(Club $club) {
        $ur = $this->getUr();
//        $club = Club::where('id',$club);
        if(!($club->urs_id == $ur->id)){
            return redirect()->route('accueil')->with('error', "La liste des adhérents du club à laquelle vous avez cherché à accéder n'appartient pas à l'UR que vous gérez");
        }
//        dd($club->numero);
//1924
        $limit_pagination = 100;
//        $adherents =  DB::table('adherents')->where('num_club', $club->numero)->orderBy('num_carte')->join('utilisateurs', 'adherents.num_carte', '=', 'utilisateurs.identifiant')->paginate($limit_pagination);
        $adherents =  DB::table('utilisateurs')->where('clubs_id', $club->id)->orderBy('identifiant')->paginate($limit_pagination);
//        dd($adherents);
        return view('urs.liste_adherents_club', compact('ur','club','adherents','limit_pagination'));
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
    public function updateClub(Club $club){
        $ur = $this->getUr();
          if(!($club->urs_id == $ur->id)){
            return redirect()->route('accueil')->with('error', "Le club que vous avez cherché à modifier n'appartient pas à l'UR que vous gérez");
        }
        list($club, $activites, $equipements, $countries) = $this->getClubFormParameters($club);
        return view('urs.update_club',compact('club','activites','countries','equipements'));
    }
    public function updateGeneralite(ClubReunionRequest $request, Club $club)
    {
        //TODO : enregistrer file sur le serveur
        $this->updateClubGeneralite($club, $request);
        return redirect()->route('UrGestion_updateClub',compact('club'))->with('success', "Les informations générales du club a été mise à jour");;
    }

    public function updateClubAddress(AdressesRequest $request, Club $club)
    {
        $this->updateClubAdress($club,$request);
        return redirect()->route('UrGestion_updateClub',compact('club'))->with('success', "L'adresse du club a été mise à jour");
    }

    public function updateReunion(ClubReunionRequest $request, Club $club)
    {
        $this->updateClubReunion($club, $request);
        return redirect()->route('UrGestion_updateClub',compact('club'))->with('success', "Les informations de réunion du club ont été mises à jour");
    }
}
