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
//        dd($term, is_numeric($term));
        if (is_numeric($term)) {
            return Commune::select('nom', 'id', 'code_postal')->where('code_postal', 'LIKE', $term.'%')->orderBy('code_postal')->orderBy('nom')->limit(15)
                ->get()
                ->map(function ($commune){
                    return [
                        'id' => $commune->id,
                        'label' => $commune->nom.' ('.$commune->code_postal.')',
                        'value' =>$commune->nom.' ('.$commune->code_postal.')'
                    ];
                });
        } else {
            return Town::select('name', 'id', 'zipcode')->where('name', 'LIKE', $term.'%')->orderBy('name')->limit(10)
                ->get()
                ->map(function ($town){
                    return [
                        'id' => $town->id,
                        'label' => $town->name.' ('.$town->zipcode.')',
                        'value' => $town->name.' ('.$town->zipcode.')'
                    ];
                });
        }
    }
}
