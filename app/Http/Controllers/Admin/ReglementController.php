<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Reglement;
use Illuminate\Http\Request;

class ReglementController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reglements = Reglement::orderByDesc('id')->paginate(100);
        foreach ($reglements as $reglement) {
            if ($reglement->clubs_id) {
                $club = Club::where('id', $reglement->clubs_id)->first();
                if ($club) {
                    $dir = $club->getImageDir();
                    if (file_exists($dir.'/'.$reglement->reference.'.pdf')) {
                        list($tmp, $dir_club) = explode('htdocs/', $dir);
                        $reglement->bordereau = env('APP_URL').$dir_club.'/'.$reglement->reference.'.pdf';
                    }
                }
            }
        }
        return view('admin.reglements.index', compact('reglements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
