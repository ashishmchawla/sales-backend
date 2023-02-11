<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Models\LeadAmount;
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
            'lead_owner' => $request->lead_owner,
        ];

        $lead = Lead::create($data);

        if( $lead ) {

            $leadAmounts = [
                'lead_id' => $lead->id,
                'month' => date('n'),
                'year' => date('Y'),
                'marginValue' => $request->marginValue,
                'mfValue' => $request->mfValue,
                'insuranceValue' => $request->insuranceValue,
                'optValue' => $request->optValue,
                'lead_owner' => $request->lead_owner
            ];

            $leadAmount = LeadAmount::create($leadAmounts);

            $leadOwner = User::find($request->lead_owner);
            $activityData = [
                'lead_id' => $lead->id,
                'activity_log' => 'New lead added by '.$leadOwner->first_name.' '.$leadOwner->last_name,
                'activity_type' => 'note',
                'remind_at' => null,
                'is_event_complete' => 1,
                'logged_by' => $request->lead_owner,
            ];
            $activity = LeadActivity::create($activityData);
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
        $lead->lead_status = $request->lead_status == 'new' ? 'existing' : $request->lead_status;
        // $lead->marginValue = $request->marginValue;
        // $lead->mfValue = $request->mfValue;
        // $lead->insuranceValue = $request->insuranceValue;
        // $lead->optValue = $request->optValue;

        if( $lead->save() ) {
            $leadOwner = User::find($request->lead_owner);
            $activityData = [
                'lead_id' => $lead->id,
                'activity_log' => 'Lead updated by '.$leadOwner->first_name.' '.$leadOwner->last_name,
                'activity_type' => 'note',
                'remind_at' => null,
                'is_event_complete' => 1,
                'logged_by' => $request->lead_owner,
            ];
            $activity = LeadActivity::create($activityData);
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
        $leadAmounts = LeadAmount::where('lead_id', $lead_id)
            ->join('lead_amounts', 'lead_amounts.lead_owner', '=', 'users.id')
            ->selectRaw('lead_amounts.*, users.first_name as owner_first_name, users.last_name as owner_last_name') 
            ->orderBy('created_at', 'asc')
            ->get();

        if ($lead) {
            return response([
                'status' => 1,
                'details' => $lead,
                'amounts' => $leadAmounts,
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

    public function getLeadDetailsForAdmin($lead_id) {

        $lead = Lead::where('leads.id', $lead_id)
            ->join('users', 'leads.lead_owner', '=', 'users.id')
            ->selectRaw('leads.*, users.first_name as owner_first_name, users.last_name as owner_last_name')
            ->orderBy('leads.created_at', 'desc')
            ->first();
        
        $leadAmounts = LeadAmount::where('lead_id', $lead_id)
            ->join('lead_amounts', 'lead_amounts.lead_owner', '=', 'users.id')
            ->selectRaw('lead_amounts.*, users.first_name as owner_first_name, users.last_name as owner_last_name') 
            ->orderBy('created_at', 'asc')->get();

        if ($lead) {
            return response([
                'status' => 1,
                'details' => $lead,
                'amounts' => $leadAmounts,
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

    public function getLeadActivities($lead_id) {

        $leadActivities = LeadActivity::where('lead_id', $lead_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if( $leadActivities ) {
            return response([
                'status' => 1,
                'details' => $leadActivities,
                'message' => 'Lead activities fetched'
            ]);
        }
        else {
            return response([
                'status' => 0,
                'error_message' => 'Something went wrong, try again'
            ]);
        }

    }

    public function addNumbers(Request $request) { 

        $leadAmounts = [
            'lead_id' => $request->lead_id,
            'month' => date('n'),
            'year' => date('Y'),
            'marginValue' => $request->marginValue,
            'mfValue' => $request->mfValue,
            'insuranceValue' => $request->insuranceValue,
            'optValue' => $request->optValue,
            'lead_owner' => $request->lead_owner
        ];

        $leadAmount = LeadAmount::create($leadAmounts);
        $leadOwner = User::find($request->lead_owner);
        $lead = Lead::find($request->lead_id);
        $activityData = [
            'lead_id' => $request->lead_id,
            'activity_log' => 'Added a new entry for '.$lead->first_name.' '.$lead->last_name.' by '.$leadOwner->first_name.' '.$leadOwner->last_name,
            'activity_type' => 'note',
            'remind_at' => null,
            'is_event_complete' => 1,
            'logged_by' => $request->lead_owner,
        ];
        $activity = LeadActivity::create($activityData);

        if( $lead->lead_status == 'new' ) {
            $lead->lead_status = 'existing';
            $lead->save();
        }

        return response(['lead' => $lead, 'status' => 1, 'message' => 'Lead updated successfully']);
  

    }

    public function editNumbers(Request $request) {

        $leadAmounts = LeadAmount::find($request->lead_amount_id);
     
        $leadAmounts->lead_id = $request->lead_id;
        $leadAmounts->month = date('n');
        $leadAmounts->year = date('Y');
        $leadAmounts->marginValue = $request->marginValue;
        $leadAmounts->mfValue = $request->mfValue;
        $leadAmounts->insuranceValue = $request->insuranceValue;
        $leadAmounts->optValue = $request->optValue;
        $leadAmounts->lead_owner = $request->lead_owner;

        $leadAmounts->save();  
        $lead = Lead::find($request->lead_id);
        $activityData = [
            'lead_id' => $request->lead_id,
            'activity_log' => $leadOwner->first_name.' '.$leadOwner->last_name.' edited the entry for '.$lead->first_name.' '.$lead->last_name,
            'activity_type' => 'note',
            'remind_at' => null,
            'is_event_complete' => 1,
            'logged_by' => $request->lead_owner,
        ];
        $activity = LeadActivity::create($activityData);
    
        if( $lead->lead_status == 'new' ) {
            $lead->lead_status = 'existing';
            $lead->save();
        }

        return response(['lead' => $lead, 'status' => 1, 'message' => 'Lead updated successfully']);
  
    }

}