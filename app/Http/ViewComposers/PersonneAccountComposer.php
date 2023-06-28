<?php

namespace App\Http\ViewComposers;

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
        $personne = session()->get('personne');
        $view->with('personne', $personne);
    }
}
