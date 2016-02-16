<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Competition;
use App\Libs\Crypt;
use Carbon\Carbon;

class CompetitionController extends Controller
{
    public function getActive($user_unique_id) {
        $competitions = Competition::getActive($user_unique_id);
        return response()->json(array("competitions" => $competitions));
    }
    public function postApply(Request $request, $user_unique_id) {
        $apply_data = Crypt::mc_decrypt(Request::input('apply_data'));
        if(count($apply_data) < 2 || !is_numeric($apply_data[1])){
            $result = array("result" => false,
                            "reason" => "FAILED",
                            "competition_id" => NULL);
            return response()->json($result);
        }
        $now_time = Carbon::now();
        if($aply_data[1] + 10 < $now_time->timestamp){
            $result = array("result" => false,
                            "reason" => "FAILED",
                            "competition_id" => $apply_data[0]);
            return response()->json($result);
        }
        $result = Competition::apply($user_unique_id, $apply_data[0]);
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
