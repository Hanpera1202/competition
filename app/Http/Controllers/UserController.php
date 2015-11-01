<?php

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{
    public function getRegist($device_id) {
        $user_id = User::regist($device_id);
        return response()->json(array("user_id" => $user_id));
    }
}
