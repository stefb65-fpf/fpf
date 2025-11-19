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
            ['layouts.menu', 'layouts.header', 'urs.gestion', 'admin.statistiques.index', 'admin.statistiques.votes', 'admin.statistiques.votesphases',
                'clubs.statistiques.index', 'urs.statistiques.index', 'urs.statistiques.votes', 'urs.statistiques.votesphases', 'formations.detail', 'formations.detail_notadherents',
                'admin.statistiques.votesdetail','admin.statistiques.listevotesbyclub', 'urs.statistiques.votesdetail','urs.statistiques.listevotesbyclub'],
            'App\Http\ViewComposers\MenuComposer'
        );

        view()->composer(
            [
                'admin.accueil',
                'admin.personnes.form',
                'admin.personnes.liste',
                'admin.urs.edit',
                'admin.urs.fonctions',
                'admin.fonctions.index',
                'admin.clubs.index',
                'admin.clubs.liste_fonctions',
                'urs.gestion',
                'layouts.menu',
                'admin.statistiques.index',
                'clubs.statistiques.index',
                'clubs.update_form_club',
                'clubs.listeAdherents',
                'clubs.adherents.form',
                'urs.statistiques.index',
                'urs.statistiques.votes',
                'urs.statistiques.votesphases',
                'urs.statistiques.votesdetail',
                'urs.statistiques.listevotesbyclub'
            ],
            'App\Http\ViewComposers\DroitsComposer'
        );

        view()->composer(
            ['layouts.accountMenu', 'clubs.gestion'],
            'App\Http\ViewComposers\FlorilegeMenuComposer'
        );

        view()->composer(
            ['admin.statistiques.votes', 'admin.statistiques.index', 'urs.statistiques.index', 'urs.statistiques.votes', 'clubs.statistiques.index', 'urs.statistiques.votesphases',
                'urs.statistiques.votesdetail', 'urs.statistiques.listevotesbyclub'],
            'App\Http\ViewComposers\VoteComposer'
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
