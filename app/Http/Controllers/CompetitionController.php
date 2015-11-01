<?php

namespace App\Http\Controllers;

use App\Competition;

class CompetitionController extends Controller
{
    public function getActive($user_id) {
        $competitions = Competition::getActive($user_id);
        return response()->json(array("competitions" => $competitions));
    }
    public function getApply($user_id, $application_id) {
        $competitions = Competition::getActive($user_id);
        return response()->json(array("result" => "success"));
    }
}
