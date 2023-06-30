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
        $user = session()->get('user');
        $view->with('user', $user);
    }
}