<?php

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{
    public function getRegist($device_id) {
        $user_unique_id = User::regist($device_id);
        return response()->json(array("user_id" => $user_unique_id));
    }
    public function getRegistmail($user_unique_id, $mail_address) {
        $user = User::where('unique_id', '=', $user_unique_id)->firstOrFail();
        // URL safe
        $mail_address = str_replace(array('_','-'), array('/', '+'), $mail_address);
        $user->mail_address = base64_decode($mail_address);
        
        return response()->json(array("result" => $user->save()));
    }
}
