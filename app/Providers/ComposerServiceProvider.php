<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(
            ['layouts.account', 'personnes.mon_profil', 'personnes.mon_compte', 'layouts.header', 'layouts.menu'],
            'App\Http\ViewComposers\PersonneAccountComposer'
        );
        view()->composer(
            ['layouts.menu', 'layouts.header'],
            'App\Http\ViewComposers\MenuComposer'
        );

        view()->composer(
            ['admin.accueil'],
            'App\Http\ViewComposers\DroitsComposer'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
