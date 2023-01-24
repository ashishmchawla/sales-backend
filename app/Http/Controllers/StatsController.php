<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTargets;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadActivity;

class StatsController extends Controller
{

    public function storeStats($month, $year) {

        $users = User::where('user_type', 'user')->get();
        $leads = Lead::where(function($q) use($month, $year) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
        })->get();

        $leadActivities = LeadActivity::where(function($q) use($month, $year) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
        })->get();

        foreach( $users as $user ) {

        }

    }

}
