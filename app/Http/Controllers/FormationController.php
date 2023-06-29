<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function __construct() {
        $this->middleware('checkLogin');
    }

    public function accueil() {
        return view('formations.accueil');
    }
}
