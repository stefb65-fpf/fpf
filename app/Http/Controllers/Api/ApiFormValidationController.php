<?php

namespace App\Http\Controllers\Api;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\Club;
use App\Models\Personne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiFormValidationController extends Controller
{
    use Tools;

    public function newsPreferences(Request $request)
    {
        $personne = Personne::where('id', $request->personne)->first();
        $datap = array('news' => $request->newspreference);
        $personne->update($datap);
//        $request->session()->put('user', $personne); session store not set on request...normal mais comment passer la session en ajax?
        $this->registerAction($personne->id, 4, "Modification de vos préférences concernant les nouvelles FPF");
        return [true];
    }

    public function clubActivite(Request $request, Club $club)
    {

        $club = Club::where('id', $request->club);
        $club_activites = DB::table('activitesclubs')->where('clubs_id', $request->club)->get();
//        $key = array_search($request->clubPreferences, $club_activites);
//        dd($club_activites);
        $activites = [];
        $isInArray = false;
        foreach ($club_activites as $activite) {
            array_push($activites, $activite->activites_id);
            if ($activite->activites_id == $request->clubPreferences) {
                $isInArray = true;
            }
        }
        if ($isInArray) {
            //on enleve la ligne correspondant de la table pivot
            DB::table('activitesclubs')->where('clubs_id', $request->club)->where('activites_id',$request->clubPreferences)->delete();
        } else {
            //on ajoute la ligne correspondant à la table pivot
            $data_ap = array('activites_id' => $request->clubPreferences, 'clubs_id' => $request->club);
            DB::table('activitesclubs')->insert($data_ap);
        }

        return [true];
    }
    public function clubEquipement(Request $request, Club $club)
    {


        $club = Club::where('id', $request->club);
        $club_equipements = DB::table('equipementsclubs')->where('clubs_id', $request->club)->get();
        $equipements = [];
        $isInArray = false;
        foreach ($club_equipements  as $equipement) {
            array_push($equipements, $equipement->equipements_id);
            if ($equipement->equipements_id == $request->clubPreferences) {
                $isInArray = true;
            }
        }
        if ($isInArray) {
            //on enleve la ligne correspondant de la table pivot
            DB::table('equipementsclubs')->where('clubs_id', $request->club)->where('equipements_id',$request->clubPreferences)->delete();
        } else {
            //on ajoute la ligne correspondant à la table pivot
            $data_ap = array('equipements_id' => $request->clubPreferences, 'clubs_id' => $request->club);
            DB::table('equipementsclubs')->insert($data_ap);
        }
        return [true];
    }
}
