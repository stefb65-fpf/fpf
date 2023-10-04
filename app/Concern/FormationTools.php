<?php

namespace App\Concern;

use App\Models\Formation;
use Illuminate\Support\Facades\DB;

trait FormationTools
{

    public function getFormationCities(Formation $formation)
    {
        $cities = "";
        $today = date("Y-m-d H:i:s");
        foreach ($formation->sessions as $session) {
            $separator = strlen($cities) ? ", " : "";
            if ($session->location && $session->start_date > $today) {
                $cities = $cities . $separator . $session->location;
            }
        }
        return $cities;
    }
}
