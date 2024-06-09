<?php

namespace App\Http\ViewComposers;

use App\Models\Configsaison;
use Illuminate\View\View;

class PersonneAccountComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = session()->get('user');
        $cartes = session()->get('cartes');
        if (!$cartes) {
            $user->renew_abo = true;
        } else {
            $numeroencours = Configsaison::where('id', 1)->first()->numeroencours;
            if ($user->is_administratif == 0) {
                $user->renew_abo = true;
                // on regarde si dans la liste des cartes, il y a une carte club
                $is_in_club = false;
                foreach ($cartes as $carte) {
                    if ($carte->clubs_id) {
                        $is_in_club = true;
                    }
                }
                // si c'est un individuel uniquement, on ne propose pas le renouvellement
                if (!$is_in_club) {
                    $user->renew_abo = false;
                } else {
                    // si c'est un adhérent club, on regarde s'il est abonné
                    if ($user->is_abonne == 1) {
                        // s'il est abonné et que le numéro de fin est supérieur au numéro en cours, on ne propose pas le renouvellement
                        if ($user->abonnement->fin > $numeroencours) {
                            $user->renew_abo = false;
                        }
                    }
                }
            }
        }
        $view->with('user', $user)->with('cartes', $cartes);
    }
}
