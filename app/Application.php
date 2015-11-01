<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Application extends Model
{
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    public static function regist($user_id, $competition_id) {

        $application = new Application;
        $application->user_id = $user_id;
        $application->competition_id = $competition_id;
        $application->save();

        return $application->id;

    }
}
