<?php

namespace App\Http\Controllers\Api;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\Personne;
use Illuminate\Http\Request;
class ApiFormValidationController extends Controller
{
    use Tools;
    public  function newsPreferences(Request $request){
        $personne = Personne::where('id', $request->personne)->first();
        $datap = array('news' => $request->newspreference);
        $personne->update($datap);
//        $request->session()->put('user', $personne); session store not set on request...normal mais comment passer la session en ajax?
        $this->registerAction($personne->id, 4, "Modification de vos préférences concernant les nouvelles FPF");
        return [true];
    }
}
