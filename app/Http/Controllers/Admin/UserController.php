<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function accueil() {
        // on cherche les alertes à afficher
        $affectations = Utilisateur::where('urs_id', 99)->where('statut', 2)->get();
        return view('admin.accueil', compact('affectations'));
    }

    public function informations() {
        return view('admin.informations');
    }
}
