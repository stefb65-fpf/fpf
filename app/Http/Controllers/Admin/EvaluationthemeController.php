<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluationstheme;
use Illuminate\Http\Request;

class EvaluationthemeController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $theme = new Evaluationstheme();
        return view('admin.evaluationthemes.create', compact('theme'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = ['name' => $request->name, 'position' => $request->position];
        $theme = Evaluationstheme::create($data);
        $themes = Evaluationstheme::where('position', '>=', $request->position)->where('id', '!=', $theme->id)->get();
        foreach ($themes as $theme) {
            $theme->update(['position' => $theme->position + 1]);
        }
        return redirect()->route('formations.parametrage')->with('success', 'La catégorie a bien été ajoutée.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($theme_id)
    {
        $theme = Evaluationstheme::where('id', $theme_id)->first();
        return view('admin.evaluationthemes.edit', compact('theme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $theme_id)
    {
        $theme = Evaluationstheme::where('id', $theme_id)->first();
        $data = ['name' => $request->name, 'position' => $request->position];
        $theme->update($data);
        return redirect()->route('formations.parametrage')->with('success', 'La catégorie a bien été modifiée.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($theme_id)
    {
        $theme = Evaluationstheme::where('id', $theme_id)->first();
        $theme->delete();
        $themes = Evaluationstheme::orderBy('position')->get();
        $num = 1;
        foreach ($themes as $theme) {
            $theme->update(['position' => $num]);
            $num++;
        }
        return redirect()->route('formations.parametrage')->with('success', 'La catégorie a bien été supprimée.');
    }
}
