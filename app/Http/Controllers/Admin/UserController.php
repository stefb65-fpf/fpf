<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct() {
        // TODO ajouter le middleware pour vérifier les droits admin
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function accueil() {
        return view('admin.accueil');
    }
}
