<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadActivity;
use Auth;

class LeadActivityController extends Controller
{
    
    public function createActivity(Request $request) {

		$user_id = Auth::user()->id;

        $data = [
            'lead_id' => $request->lead_id,
            'activity_log' => $request->activity_log,
            'activity_type' => $request->activity_type,
            'remind_at' => $request->remind_at,
            'is_event_complete' => 0, 
            'logged_by' => $user_id,            
        ];

        $activity = LeadActivity::create($data);

        return response([
            'status' => 1, 
            'activity' => $activity, 
            'message' => 'Activity added successfully'
        ]);

    }

    public function editActivity($activity_id, Request $request) {

        $activity = LeadActivity::find($activity_id);
        $activity->activity_log = $request->activity_log; 
        $activity->activity_type = $request->activity_type;
        $activity->remind_at = $request->remind_at;
        $date = new DateTime($request->remind_at);
        $now = new DateTime();
        if($date < $now) {
            $activity->is_event_complete = 1; 
        } 
        else {
            $activity->is_event_complete = 0;
        }
		$activity->logged_by = Auth::user()->id;

        if($activity->save()) {
            return response([
                'status' => 1, 
                'activity' => $activity,
                'message' => 'Activity updated successfully'
            ]);
        }
        else {
            return response([
                'status' => 0, 
                'error_message' => 'Something went wrong, please try again.'
            ]);
        }

    }

    public function deleteActivity($activity_id) {

        $activity = LeadActivity::find($activity_id);

        if( $activity->delete() ) {
            return response([
                'status' => 1, 
                'message' => 'Activity deleted successfully'
            ]);
        }
        else { 
            return response([
                'status' => 0, 
                'error_message' => 'Something went wrong, please try again'
            ]);
        }

    }

}
