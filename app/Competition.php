<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Competition extends Model
{
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    public static function getActive($user_id) {
        $now_time = Carbon::now();
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
                ->where('competitions.start_date', '<=', $now_time->toDateTimeString())
                ->where('competitions.end_date', '>=', $now_time->toDateTimeString())
                ->groupBy('competitions.id')
                ->get();
                //->toSql();

        return $competition;
    }

    public static function getData($competition_id){
        $competition = 
            DB::table('competitions')
                ->select(DB::raw("competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.start_date,".
                                 "competitions.end_date,competitions.apply_num,".
                                 "items.point"))
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->where('competitions.id', '=', $competition_id)
                ->get();
        if(count($competition) > 0){
            return $competition[0]; 
        }
        return false;
    }
    
    public static function apply($user_id, $competition_id) {
        $now_time = Carbon::now();
        $competition = self::getData($competition_id);
        if($competition == false ||
           $competition->start_date > $now_time->toDateTimeString() ||
           $competition->end_date < $now_time->toDateTimeString()){
            return false;
        }

        DB::transaction(function() use ($user_id, $competition_id){

            $application = new Application;
            $application->regist($user_id, $competition_id);
            $competition = new Competition;
            $competition->where('id', '=', $competition_id)
                        ->increment('apply_num', 1);
        });

        return $competition->point;

    }
}
