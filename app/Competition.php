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
           $competition->start_date > $now_time->toDateTimeString()){
            return array("result" => false,
                         "reason" => "FAILED",
                         "competition_id" => $competition_id);
        }
        if($competition->end_date < $now_time->toDateTimeString()){
            return array("result" => false, 
                         "reason" => "ENDED",
                         "competition_id" => $competition_id);
        }

        DB::transaction(function() use ($user_id, $competition_id){

            $application = new Application;
            $application->regist($user_id, $competition_id);
            $competition = new Competition;
            $competition->where('id', '=', $competition_id)
                        ->increment('apply_num', 1);
        });

        return array("result" => true,
                     "reason" => "SUCCESS",
                     "competition_id" => $competition->id);

    }

    public static function getResults($user_id) {
        $now_time = Carbon::now();
        $results = 
            DB::table('applications')
                ->select(DB::raw("max(applications.win_flag) as result,".
                                 "competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.end_date,".
                                 "competitions.apply_num,items.point,".
                                 "count(applications.id) as my_apply_num"))
                ->Join('competitions', 'applications.competition_id', '=', 'competitions.id')
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->where('applications.user_id', '=', $user_id)
                ->groupBy('competitions.id')
                ->get();
                //->toSql();

        foreach($results as $key => $result){
            if($result->end_date > $now_time->toDateTimeString()){
                $results[$key]->progress = "1";
            }elseif(is_null($result->result)){
                $results[$key]->progress = "2";
            }else{
                $results[$key]->progress = "3";
            }
        }

        return $results;
    }

    public static function getResult($user_id, $competition_id) {
        $now_time = Carbon::now();
        $result = 
            DB::table('applications')
                ->select(DB::raw("max(applications.win_flag) as result,".
                                 "competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.end_date,".
                                 "competitions.apply_num,items.point,".
                                 "count(applications.id) as my_apply_num"))
                ->Join('competitions', 'applications.competition_id', '=', 'competitions.id')
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->where('applications.user_id', '=', $user_id)
                ->where('applications.competition_id', '=', $competition_id)
                ->groupBy('competitions.id')
                ->first();
                //->toSql();

        if($result->end_date > $now_time->toDateTimeString()){
            $result->progress = "1";
        }elseif(is_null($result->result)){
            $result->progress = "2";
        }else{
            $result->progress = "3";
        }

        return $result;
    }
}
