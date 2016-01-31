<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Competition extends Model
{
    public static function getActive($user_unique_id) {
        $user = User::where('unique_id', '=', $user_unique_id)->first();
        if(!isset($user->id)){
            return false;
        }
        $user_id = $user->id;
        $now_time = Carbon::now();
        $competition = 
            DB::table('competitions')
                ->select(DB::raw("competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.end_date,".
                                 "competitions.application_num as total_application_num,".
                                 "ifnull(applications.application_num, 0) as application_num,".
                                 "items.point"))
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->leftJoin('applications', function($leftJoin) use ($user_id)
                {
                    $leftJoin->on('competitions.id', '=', 'applications.competition_id')
                             ->where('applications.user_id', '=', $user_id);
                })
                ->where('competitions.start_date', '<=', $now_time->toDateTimeString())
                ->where('competitions.end_date', '>=', $now_time->toDateTimeString())
                ->get();
                //->toSql();

        return $competition;
    }

    public static function getData($competition_id){
        $competition = 
            DB::table('competitions')
                ->select(DB::raw("competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.start_date,".
                                 "competitions.end_date,".
                                 "competitions.application_num as total_application_num,".
                                 "items.point"))
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->where('competitions.id', '=', $competition_id)
                ->get();
        if(count($competition) > 0){
            return $competition[0]; 
        }
        return false;
    }
    
    public static function apply($user_unique_id, $competition_id) {
        $user = User::where('unique_id', '=', $user_unique_id)->first();
        if(!isset($user->id)){
            return array("result" => false,
                         "reason" => "FAILED",
                         "competition_id" => $competition_id);
        }
        $user_id = $user->id;
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
            $application = Application::firstOrCreate(['user_id' => $user_id,
                                                       'competition_id' => $competition_id]);
            $application->increment('application_num', 1);
            $competition = Competition::find($competition_id);
            $competition->increment('application_num', 1);
        });

        return array("result" => true,
                     "reason" => "SUCCESS",
                     "competition_id" => $competition->id);

    }

    public static function getResults($user_unique_id) {
        $user = User::where('unique_id', '=', $user_unique_id)->first();
        if(!isset($user->id)){
            return false;
        }
        $user_id = $user->id;
        $now_time = Carbon::now();
        $results = 
            DB::table('applications')
                ->select(DB::raw("applications.result,".
                                 "competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.end_date,".
                                 "competitions.application_num as total_application_num,".
                                 "applications.application_num as application_num,".
                                 "items.point"))
                ->Join('competitions', 'applications.competition_id', '=', 'competitions.id')
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->where('applications.user_id', '=', $user_id)
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

    public static function getResult($user_unique_id, $competition_id) {
        $user = User::where('unique_id', '=', $user_unique_id)->firstOrFail();
        if(!isset($user->id)){
            return false;
        }
        $user_id = $user->id;
        $now_time = Carbon::now();
        $result = 
            DB::table('applications')
                ->select(DB::raw("applications.result,".
                                 "competitions.id,items.name,items.image_url,".
                                 "competitions.win_num,competitions.end_date,".
                                 "competitions.application_num as total_application_num,".
                                 "applications.application_num as application_num,".
                                 "items.point"))
                ->Join('competitions', 'applications.competition_id', '=', 'competitions.id')
                ->join('items', 'competitions.item_id', '=', 'items.id')
                ->where('applications.user_id', '=', $user_id)
                ->where('applications.competition_id', '=', $competition_id)
                ->first();
                //->toSql();

        if(!$result){
            return false;
        }

        if($result->end_date > $now_time->toDateTimeString()){
            $result->progress = "1";
        }elseif($result->result == "0"){
            $result->progress = "2";
        }else{
            $result->progress = "3";
        }

        return $result;
    }
}
