<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscrit;
use App\Models\Session;
use Illuminate\Http\Request;

class InscritController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function liste(Session $session) {
        return view('admin.inscrits.liste', compact('session'));
    }

    public function destroy(Inscrit $inscrit) {
        $session = $inscrit->session;
        $inscrit->delete();
        return redirect()->route('inscrits.liste', $session)->with('success', 'Inscription supprimée avec succès');
    }
}
