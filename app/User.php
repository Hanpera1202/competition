<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class User extends Model
{
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    public static function regist($device_id) {

        $user = new User;
        $user->device_id = $device_id;
        $user->save();

        return $user->id;

    }
}
