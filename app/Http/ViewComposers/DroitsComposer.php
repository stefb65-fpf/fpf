<?php

namespace App\Http\ViewComposers;

use App\Models\Droit;
use App\Models\Fonction;
use Illuminate\View\View;

class DroitsComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // on récupère les droits utilisateurs pour affichage dans les pages de gestion
        $cartes = session()->get('cartes');
        $tab_droits = []; // tableau des droits de l'utilisateur
        if ($cartes) {
            foreach($cartes[0]->droits as $droit) {
                $tab_droits[] = $droit->label;
            }
            foreach (session()->get('cartes')[0]->fonctions as $fonction) {
                foreach ($fonction->droits as $droit) {
                    $tab_droits[] = $droit->label;
                }
                if ($fonction->parent_id) {
                    $parent = Fonction::where('id', $fonction->parent_id)->first();
                    foreach ($parent->droits as $droit) {
                        $tab_droits[] = $droit->label;
                    }
                }
            }
        } else {
            $user = session()->get('user');
            if ($user->is_administratif) {
                if ($user->nom == 'CLOSSE') {
                    $droits = Droit::all();
                } else {
                    $droits = Droit::whereNotIn('label', ['GESDRO', 'GESPARAM', 'GESVOT'])->get();
                }
                foreach ($droits as $droit) {
                    $tab_droits[] = $droit->label;
                }
            }
        }
        $view->with('droits_fpf', $tab_droits);
    }
}
