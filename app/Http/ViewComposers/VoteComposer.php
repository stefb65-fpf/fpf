<?php

namespace App\Http\ViewComposers;

use App\Models\Vote;
use Illuminate\View\View;

class VoteComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // on prend la date d'une jour moins une semaine
        $fin = date('Y-m-d', strtotime('-7 days'));
        $vote = Vote::where('type', 1)->where('phase', '>', 0)->where('urs_id', 0)->where('fin', '>=', $fin)->first();
        $exist_vote = (bool)$vote;
        $view->with('exist_vote', $exist_vote);
    }
}
