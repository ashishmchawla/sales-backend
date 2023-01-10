<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
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

}
