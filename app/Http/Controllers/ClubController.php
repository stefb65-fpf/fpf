<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function __construct() {
        // TODO ajouter le middleware pour vérifier les droits clubs
        $this->middleware(['checkLogin', 'clubAccess']);
    }

    public function gestion() {
        return view('clubs.gestion');
    }
}
