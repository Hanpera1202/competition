<?php

namespace App\Http\Controllers;

use App\Competition;
use App\Libs\Crypt;

class CompetitionController extends Controller
{
    public function getActive($user_unique_id) {
        $competitions = Competition::getActive($user_unique_id);
        return response()->json(array("competitions" => $competitions));
    }
    public function getApply($user_unique_id, $crypt_competition_id) {
        $competition_id = Crypt::mc_decrypt($crypt_competition_id);
        $result = Competition::apply($user_unique_id, $competition_id);
        return response()->json($result);
    }
    public function getResults($user_unique_id) {
        $results = Competition::getResults($user_unique_id);
        return response()->json(array("results" => $results));
    }
    public function getResult($user_unique_id, $competition_id) {
        $result = Competition::getResult($user_unique_id, $competition_id);
        return response()->json($result);
    }
}
