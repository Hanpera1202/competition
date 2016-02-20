<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Competition;
use App\Libs\Crypt;
use Carbon\Carbon;

class CompetitionController extends Controller
{
    public function getIndex(Request $request) {
        $competitions = Competition::getActive($request->input("user_unique_id"));
        return response()->json(array("competitions" => $competitions));
    }

}
