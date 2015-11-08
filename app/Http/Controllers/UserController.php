<?php

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{
    public function getRegist($device_id) {
        $user_id = User::regist($device_id);
        return response()->json(array("user_id" => $user_id));
    }
    public function getRegistmail($user_id, $mail_address) {
        $user = User::find($user_id);
        // URL safe
        $mail_address = str_replace(array('_','-'), array('/', '+'), $mail_address);
        $user->mail_address = base64_decode($mail_address);
        
        return response()->json(array("result" => $user->save()));
    }
}
