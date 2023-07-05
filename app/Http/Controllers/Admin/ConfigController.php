<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configsaison;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess'])->except(['updateTarif', 'updateConfig']);
    }

    public function index()
    {
        // on récupère les paramètres de config de la saison en cours
        $config = Configsaison::where('id', 1)->first();

        // on récupère les paramètres de config de la saison prochaine
        $configNext = Configsaison::where('id', 2)->first();

        // récupération des tarifs de la saison en cours
        $tarifs = Tarif::where('statut', 0)->where('position', '<>', 0)->orderBy('position')->get();

        // récupération des tarifs de la saison prochaine
        $tarifsNext = Tarif::where('statut', 1)->where('position', '<>', 0)->orderBy('position')->get();

        return view('admin.config.index', compact('config', 'configNext', 'tarifs', 'tarifsNext'));
    }

    public function updateTarif(Request $request) {
        if (filter_var($request->tarif, FILTER_VALIDATE_FLOAT) === false)
            return response()->json(['error' => "Le tarif n'est pas valide"], 400);

        $tarif = Tarif::where('id', $request->ref)->where('statut', $request->statut)->first();
        if (!$tarif) {
            return response()->json(['error' => "Le tarif n'existe pas"], 400);
        }
        $data = array('tarif' => $request->tarif);
        Tarif::where('id', $request->ref)->where('statut', $request->statut)->update($data);

        return response()->json(['success' => "Le tarif a été modifié"], 200);
    }

    public function updateConfig(Request $request) {
        $config = Configsaison::where('id', $request->id)->first();
        if (!$config) {
            return response()->json(['error' => "La configuration n'existe pas"], 400);
        }

        $data = array($request->ref => $request->value);
        Configsaison::where('id', $request->id)->update($data);
        return response()->json(['success' => "Le paramètre a été modifié"], 200);
    }
}
