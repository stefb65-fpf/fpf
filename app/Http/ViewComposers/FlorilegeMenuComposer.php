<?php

namespace App\Http\ViewComposers;

use App\Models\Configsaison;
use App\Models\Invoice;
use Illuminate\View\View;

class FlorilegeMenuComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // on vérifie sir le menu Folrilège est à afficher
        $florilege = Configsaison::where('id', 1)->selectRaw('datedebutflorilege, datefinflorilege')->first();
        $florilege->active = (date('Y-m-d') >= $florilege->datedebutflorilege && date('Y-m-d') <= $florilege->datefinflorilege);

        $menu = session()->get('menu');
        // on regarde si la personne a des factures à titre individuel et au titre d'un club
        $individual_invoices = Invoice::where('personne_id', session()->get('user')->id)->count();

        if ($menu['club']) {
            $carte = session()->get('cartes')[0];
            $club_invoices = Invoice::where('club_id', $carte->clubs_id)->count();
        } else {
            $club_invoices = 0;
        }


        $view->with('florilege', $florilege)->with('individual_invoices', $individual_invoices)->with('club_invoices', $club_invoices);
    }
}
