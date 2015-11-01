<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Competition extends Model
{
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    public static function getActive($user_id) {

        // get active competitions
        $competition = 
            DB::table('competitions')
                ->select(DB::raw("competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.end_date,".
                                 "competitions.apply_num,items.point,".
                                 "count(applications.id) as my_apply_num"))
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->leftJoin('applications', function($leftJoin) use ($user_id)
                {
                    $leftJoin->on('competitions.id', '=', 'applications.competition_id')
                             ->where('applications.user_id', '=', $user_id);
                })
                ->where('competitions.start_date', '<=', date('Y-m-d H:i:s'))
                ->where('competitions.end_date', '>=', date('Y-m-d H:i:s'))
                ->groupBy('competitions.id')
                ->get();
                //->toSql();

        return $competition;
    }
    
    public static function apply($user_id, $competition_id) {

        $application = new Application;

        if($application->regist($user_id, $competition_id)) {
            $competition = new Competition;
            $competition->where('id', '=', $competition_id)
                        ->increment('apply_num', 1);
        }

        return true;

    }
}
