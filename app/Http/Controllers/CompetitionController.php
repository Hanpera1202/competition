<?php

namespace App\Http\Controllers;

use App\Competition;

class CompetitionController extends Controller
{
    public function getActive($user_id) {
        $competitions = Competition::getActive($user_id);
        return response()->json(array("competitions" => $competitions));
    }
    public function getApply($user_id, $competition_id) {
        $points = Competition::apply($user_id, $competition_id);
        return response()->json(array("decrease_point" => $points));
    }
    public function getResult($user_id) {
        $results = Competition::getResult($user_id);
        return response()->json(array("results" => $results));
    }
}
