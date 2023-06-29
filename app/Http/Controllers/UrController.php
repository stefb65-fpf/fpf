<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrController extends Controller
{
    public function __construct() {
        // TODO ajouter le middleware pour vérifier les droits ur
        $this->middleware(['checkLogin', 'urAccess']);
    }

    public function gestion() {
        return view('urs.gestion');
    }
}
