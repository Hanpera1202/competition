<?php

namespace App\Http\Controllers;

use App\Competition;

class CompetitionController extends Controller
{
    public function getActive() {
        $competitions = Competition::getActive();
        return response()->json($competitions);
    }
}
