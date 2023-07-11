<?php

namespace App\Http\ViewComposers;

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
        $cartes = session()->get('cartes');
        $tab_droits = []; // tableau des droits de l'utilisateur
        foreach($cartes[0]->droits as $droit) {
            $tab_droits[] = $droit->label;
        }
        $view->with('droits_fpf', $tab_droits);
    }
}