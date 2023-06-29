<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class MenuComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $menu = session()->get('menu');
        $view->with('menu', $menu);
    }
}
