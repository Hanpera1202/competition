<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    //
    public static function getActive() {
        $competition = Competition::join('items', 'competitions.item_id', '=', 'items.id')
            ->where('competitions.start_date', '<=', date('Y-m-d H:i:s'))
            ->where('competitions.end_date', '>=', date('Y-m-d H:i:s'))
            ->get();

        return $competition;
    }
}
