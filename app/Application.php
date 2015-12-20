<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Application extends Model
{

    protected $fillable = ['id', 'user_id', 'competition_id'];

}
