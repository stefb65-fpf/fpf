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
            ['admin.accueil', 'admin.personnes.form', 'urs.gestion'],
            'App\Http\ViewComposers\DroitsComposer'
        );

        view()->composer(
            ['layouts.accountMenu', 'clubs.gestion'],
            'App\Http\ViewComposers\FlorilegeMenuComposer'
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
