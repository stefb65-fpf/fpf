<?php

namespace App\Concern;

use App\Models\Formation;
use Illuminate\Support\Facades\DB;

trait FormationTools
{

    public function getFormationCities(Formation $formation)
    {
        $cities = [];
        foreach ($formation->sessions as $session) {
            if ($session->location) {
                array_push($cities, $session->location);
            }
        }
        return $cities;
    }


}
