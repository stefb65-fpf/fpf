<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\Personne;
use Illuminate\Http\Request;
class ApiFormValidationController extends Controller
{
    public  function newsPreferences(Request $request){
        $personne = Personne::where('id', $request->personne)->first();
        $datap = array('news' => $request->newspreference);
        $personne->update($datap);
//        $request->session()->put('user', $personne);
        return [true];
    }
}
