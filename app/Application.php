<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Application extends Model
{
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    public static function regist($user_id, $competition_id) {

        $now_time = Carbon::now();

        $application = new Application;
        $application->user_id = $user_id;
        $application->competition_id = $competition_id;
        $application->apply_date = $now_time->toDateTimeString();
        $application->save();

        return $application->id;

    }
}
