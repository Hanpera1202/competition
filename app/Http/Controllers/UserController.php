<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Competition;
use App\Libs\Crypt;

class UserController extends Controller
{
    public function postCreate() {
        $user_unique_id = User::regist();
        return response()->json(array("user_id" => $user_unique_id));
    }

    public function postUpdate(Request $request, $user_unique_id) {
        $mail_address = Crypt::mc_decrypt($request->input('mail_address'));
        $user = User::where('unique_id', '=', $user_unique_id)->firstOrFail();
        $user->mail_address = $mail_address;
        return response()->json(array("result" => $user->save()));
    }

    public function postApplication(Request $request, $user_unique_id) {
        $decoded_apply_data = Crypt::mc_decrypt($request->input('apply_data'));
        parse_str($decoded_apply_data, $apply_data);
        if(count($apply_data) < 2 || !is_numeric($apply_data["competition_id"])){
            $result = array("result" => false,
                            "reason" => "FAILED",
                            "competition_id" => NULL);
            return response()->json($result);
        }
        $now_time = Carbon::now();
        if($apply_data["timestamp"] + 10 < $now_time->timestamp){
            $result = array("result" => false,
                            "reason" => "FAILED",
                            "competition_id" => $apply_data["competition_id"]);
            return response()->json($result);
        }
        $result = Competition::apply($user_unique_id, $apply_data["competition_id"]);
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
