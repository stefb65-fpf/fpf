<?php

namespace App\Concern;

use App\Models\Formation;
use App\Models\Personne;
use Illuminate\Support\Facades\DB;

trait FormationTools
{

    public function getFormationCities(Formation $formation, $default_location = null)
    {
        $cities = $default_location ? [$default_location] : [];
        $today = date("Y-m-d H:i:s");
        foreach ($formation->sessions as $session) {
            if ($session->location && $session->start_date > $today) {
                array_push($cities, $session->location);
            }
        }
       $cities = implode(", ",array_unique($cities));
        return $cities;
    }

}
