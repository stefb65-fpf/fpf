<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Commune;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    public function autocompleteCommune(Request $request)
    {
        $term = $request->get('term');
        if (is_numeric($term)) {
            return Commune::select('nom', 'id', 'code_postal')->where('code_postal', 'LIKE', $term.'%')->orderBy('code_postal')->orderBy('nom')->limit(15)
                ->get()
                ->map(function ($commune){
                    return [
                        'id' => $commune->id,
                        'label' => $commune->nom.' ('.$commune->code_postal.')',
                        'name' =>$commune->nom,
                        'zip' =>$commune->code_postal
                    ];
                });
        } else {
            return Commune::select('nom', 'id', 'code_postal')->where('nom', 'LIKE', $term.'%')->orderBy('nom')->limit(10)
                ->get()
                ->map(function ($commune){
                    return [
                        'id' => $commune->id,
                        'label' => $commune->nom.' ('.$commune->code_postal.')',
                        'name' =>$commune->nom,
                        'zip' =>$commune->code_postal
                    ];
                });
        }
    }
}
