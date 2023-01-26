<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Auth;

class LeadController extends Controller
{

    public function createLead(Request $request) {
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact' => $request->contact,
            'location' => $request->location,
            'account_category' => $request->account_category,
            'account_code' => $request->account_code,
            'third_party' => $request->third_party,
            'lead_owner' => $request->lead_owner,
        ];

        $lead = Lead::create($data);

        if( $lead ) {
            $leadOwner = User::find($request->lead_owner);
            $activityData = [
                'lead_id' => $lead->lead_id,
                'activity_log' => 'New lead added by '.$leadOwner->first_name.' '.$leadOwner->last_name,
                'activity_type' => 'note',
                'remind_at' => null,
                'is_event_complete' => 1,
                'logged_by' => $request->lead_owner,
            ];
            $activity = LeadActivity::create($data);
        }

        return response(['status' => 1, 'lead' => $lead, 'message' => 'Lead added successfully']);


    }

    public function editLead(Request $request) {

        $lead_id = $request->lead_id;
        $lead = Lead::find($lead_id);

        $lead->first_name = $request->first_name;
        $lead->last_name = $request->last_name;
        $lead->contact = $request->contact;
        $lead->location = $request->location;
        $lead->account_category = $request->account_category;
        $lead->account_code = $request->account_code;
        $lead->third_party = $request->third_party;
        $lead->lead_status = $request->lead_status;
        if( $request->stock_margin != null ) {
            $lead->stock_margin = $request->stock_margin;
        }
        if( $lead->save() ) {
            return response(['lead' => $lead, 'status' => 1, 'message' => 'Lead updated successfully']);
        }
        else {
            return response(['status' => 0, 'error_message' => 'Failed to update, please try again']);
        }
    }

    public function searchLeadByName(Request $request) {

        $search = "%".$request->searchWord."%";
        $leads = Lead::where(function($q) use($search) {
            $q->where('first_name', 'like', $search);
            $q->orWhere('last_name', 'like', $search);
        })
        ->get();

        if( $leads->count() > 0 ) {
            return response([
                'status' => 1,
                'results' => $leads,
                'message' => $leads->count().' leads found'
            ]);
        }
        else {
            return response(['status' => 0, 'error_message' => 'No leads found']);
        }
    }

    public function getLeads() {

		$user_id = Auth::user()->id;

        $leads = Lead::where('lead_owner', $user_id)->get();
        if( $leads->count() > 0 ) {
            return response(['status' => 1, 'results' => $leads, 'message' => 'Leads found']);
        }
        else {
            return response(['status' => 0, 'error_message' => 'No leads found']);
        }

    }

    public function getLeadDetails($lead_id) {

        $lead = Lead::where('id', $lead_id)->with('activities')->first();

        if ($lead) {
            return response([
                'status' => 1,
                'details' => $lead,
                'message' => 'Lead details fetched'
            ]);
        }
        else {
            return response([
                'status' => 0,
                'error_message' => 'Something went wrong, try again'
            ]);
        }

    }

    public function allLeads(){
        
        $leads = Lead::join('users', 'leads.lead_owner', '=', 'users.id')
        ->selectRaw('leads.*, users.first_name as owner_first_name, users.last_name as owner_last_name')
        ->orderBy('leads.created_at', 'desc')
        ->get();
        
        $result['data']=[];

        foreach ($leads as $value) {
        
			array_push($result['data'],[
				$value->id,
				$value->first_name. ' '.$value->last_name,
				$value->email,
                $value->phone,
                $value->location,
                $value->owner_first_name.' '.$value->owner_last_name,
                $value->lead_status,
                date('d M Y h:i a', strtotime($value->created_at)) 
			]);   
        }
        return $result;
	}

}