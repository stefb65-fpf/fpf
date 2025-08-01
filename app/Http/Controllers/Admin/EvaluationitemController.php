<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluationsitem;
use App\Models\Evaluationstheme;
use Illuminate\Http\Request;

class EvaluationitemController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createForTheme($theme_id) {
        $theme = Evaluationstheme::where('id', $theme_id)->first();
        if (!$theme) {
            return redirect()->route('formations.parametrage')->with('error', "Le thème d'évaluation n'existe pas.");
        }
        $item = new Evaluationsitem();
        return view('admin.evaluationitems.create', compact('item', 'theme'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeForTheme(Request $request, $theme_id)
    {
        $theme = Evaluationstheme::where('id', $theme_id)->first();
        if (!$theme) {
            return redirect()->route('formations.parametrage')->with('error', "Le thème d'évaluation n'existe pas.");
        }
        $data = ['name' => $request->name, 'position' => $request->position, 'evaluationstheme_id' => $theme->id, 'type' => $request->type];
        $item = Evaluationsitem::create($data);
        $items = Evaluationsitem::where('position', '>=', $request->position)->where('id', '!=', $item->id)->get();
        foreach ($items as $item) {
            $item->update(['position' => $item->position + 1]);
        }
        return redirect()->route('formations.parametrage')->with('success', "L'item d'évaluation a bien été modifié.");
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($item_id)
    {
        $item = Evaluationsitem::where('id', $item_id)->first();
        return view('admin.evaluationitems.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $item_id)
    {
        $item = Evaluationsitem::where('id', $item_id)->first();
        $data = ['name' => $request->name, 'position' => $request->position, 'type' => $request->type];
        $item->update($data);
        return redirect()->route('formations.parametrage')->with('success', "L'item d'évaluation a bien été modifié.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($item_id)
    {
        $item = Evaluationsitem::where('id', $item_id)->first();
        $item->delete();
        $items = Evaluationsitem::orderBy('position')->get();
        $num = 1;
        foreach ($items as $item) {
            $item->update(['position' => $num]);
            $num++;
        }
        return redirect()->route('formations.parametrage')->with('success', "L'item d'évaluation a bien été supprimé.");
    }
}
