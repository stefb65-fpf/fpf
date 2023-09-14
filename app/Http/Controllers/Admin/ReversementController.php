<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Models\Reversement;
use Illuminate\Http\Request;

class ReversementController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index() {
        if (!$this->checkDroit('GESTRE')) {
            return redirect()->route('accueil');
        }
        return view('admin.reversements.index');
    }

    public function attente() {
        if (!$this->checkDroit('GESTRE')) {
            return redirect()->route('accueil');
        }
        return view('admin.reversements.attente');
    }

    public function effectues() {
        if (!$this->checkDroit('GESTRE')) {
            return redirect()->route('accueil');
        }
        $reversements = Reversement::orderByDesc('id')->paginate(100);
        foreach ($reversements as $reversement) {
            list($annee,$ur, $tmp) = explode('-', $reversement->reference);
            $reversement->bordereau = url('storage/app/public/uploads/bordereauxur/20'.$annee).'/bordereau-ur-'.$reversement->reference.'.pdf';
        }
        return view('admin.reversements.effectues', compact('reversements'));
    }
}
