<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Ur;
use Illuminate\Http\Request;

class TopBarController extends Controller
{
    public function isAdmin()
    {
        $isAdmin = session()->get('menu')['admin'];
        $ur_id="0";
//        $isAdmin= false;
        if(!$isAdmin){
            $cartes = session()->get('cartes');
            if (!$cartes || count($cartes) == 0) {
                return redirect()->route('accueil')->with('error', "Un problème est survenu lors de la récupération des informations UR");
            }
            $active_carte = $cartes[0];
            $ur = Ur::where('id', $active_carte->urs_id)->first();
            $ur_id=$ur->id;
        }
        return compact('isAdmin','ur_id');
    }

}
