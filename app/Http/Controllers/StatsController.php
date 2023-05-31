<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserTargets;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadAmount;

class StatsController extends Controller
{

    public function storeStats($month, $year) {

        $users = User::where('user_type', 'user')->get();
       
        foreach($users as $user) {
            self::saveNewCount($user, $month, $year);
            self::saveExistingCount($user, $month, $year);
            self::saveAccountCount($user, $month, $year);
            self::saveMFCount($user, $month, $year);
            self::saveInsuranceCount($user, $month, $year);
            self::saveMarginCount($user, $month, $year);
            self::saveThirdPartyCount($user, $month, $year);
        }

    }

    public function setUserTargets(Request $request) {

        $user_id = $request->user_id;
        $month = $request->month;
        $year = $request->year;
        $new = $request->new;
        $existing = $request->existing;
        $account = $request->account;
        $margin = $request->margin;
        $mututal_funds = $request->mutual_funds;
        $insurance = $request->insurance;
        $third_party = $request->third_party;

        if( $new ) {
            $newData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'new',
                'targets' => $new
            ];
            $addNewData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'new'
            ], $newData);
        }
        if( $existing ) {
            $existingData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'existing',
                'targets' => $existing
            ];
            $addExistingData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'existing'
            ], $existingData);
        }
        if( $account ) {
            $accountData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'account',
                'targets' => $account
            ];
            $addAccountData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'account'
            ], $accountData);
        }
        if( $margin ) {
            $marginData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'margin',
                'targets' => $margin
            ];
            $addMarginData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'margin'
            ], $marginData);
        }
        if( $mututal_funds ) {
            $mfData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'mutual_funds',
                'targets' => $mututal_funds
            ];
            $addMFData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'mutual_funds'
            ], $mfData);
        }
        if( $insurance ) {
            $insuranceData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'insurance',
                'targets' => $insurance
            ];
            $addInsuranceData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'insurance'
            ], $insuranceData);
        }
        if( $third_party ) {
            $third_partyData = [
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'option_brains',
                'targets' => $third_party
            ];
            $addThirdPartyData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'option_brains'
            ], $third_partyData);
        }

        return response([
            'status' => 1,
            'message' => 'Targets added fetched'
        ]);
    

    }

    public function getUserTargetTable($user_id) {

        $userTargets = UserTargets::where('user_id', $user_id)
            ->orderBy('month', 'desc')
            ->orderBy('year', 'desc')
            ->orderBy('target_type', 'asc')
            ->get();

        $result['data']=[];
        
        foreach($userTargets as $target) {
            $monthNum  = $target->month;
            $monthName =  date('F', mktime(0, 0, 0, $monthNum, 10));
            array_push(
                $result['data'],
                [
                    $monthName.' - '.$target->year,
                    ucfirst($target->target_type),
                    $target->count,
                    $target->targets
                ]
            );
        }

        return $result;

    }

    public function saveNewCount($user, $month, $year) {

        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
        })->get();

        $newLeadsAdded = $leadAdded->count();

        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'new',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'new',
            'count' => $newLeadsAdded
        ]);

    }

    public function saveExistingCount($user, $month, $year) {

        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('updated_at', $month);
            $q->whereYear('updated_at', $year);
            $q->where('lead_owner', $user->id);
            $q->where('existingCount','>', 0);
        })
        ->get();

        $existingLeads = 0;
        foreach( $leadAdded as $lead ) {
            $existingLeads = $existingLeads + $lead->existingCount;
        }

        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'existing',
        ];

        $addExistingData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'existing',
            'count' => $existingLeads
        ]);

    }

    public function saveAccountCount($user, $month, $year) {

        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
            $q->whereNotNull('account_code');
        })->get();

        $newAccount = $leadAdded->count();

        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'account',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'account',
            'count' => $newAccount
        ]);

    }
    
    public function saveMFCount($user, $month, $year) {

        $leadAdded = LeadAmount::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
        })->get();

        $newAccount = 0; 
        foreach( $leadAdded as $la ){ 
            $newAccount = $newAccount + $la->mfValue;
        }

        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'mutual_funds',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'mutual_funds',
            'count' => $newAccount
        ]);
        
    }

    public function saveInsuranceCount($user, $month, $year) {

        $leadAdded = LeadAmount::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
        })->get();

        $newAccount = 0; 
        foreach( $leadAdded as $la ){ 
            $newAccount = $newAccount + $la->insuranceValue;
        }


        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'insurance',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'insurance',
            'count' => $newAccount
        ]);

    }
    
    public function saveMarginCount($user, $month, $year) {

        $leadAdded = LeadAmount::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
        })->get();

        $newAccount = 0; 
        foreach( $leadAdded as $la ){ 
            $newAccount = $newAccount + $la->marginValue;
        }

        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'margin',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'margin',
            'count' => $newAccount
        ]);

    }
    
    public function saveThirdPartyCount($user, $month, $year) {
    
        $leadAdded = LeadAmount::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
        })->get();


        $newAccount = 0; 
        foreach( $leadAdded as $la ){ 
            $newAccount = $newAccount + $la->optValue;
        }

        $baseData = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'option_brains',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'option_brains',
            'count' => $newAccount
        ]);        

    }
    
    public function getUserStats($user_id) {

        $month = date('n');
        $year = date('Y');
        $allTargets = 0; 
        $allCounts = 0;
        $targets = UserTargets::where('user_id', $user_id)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $data = [
            'new' => [],
            'existing' => [], 
            'account' => [],
            'margin' => [],
            'mutual_funds' => [],
            'insurance' => [],
            'option_brains' => []
        ]; 

        $counts = [
            'target' => 0,
            'achieved' => 0, 
            'follow_ups' => 0,
            'fulfilled' => 0
        ];

        foreach( $targets as $target ) {
            if( $target->target_type == 'new' && $target->targets != null ) {
                array_push( $data['new'], $target->count);
                array_push( $data['new'], $target->targets);
            }
            if( $target->target_type == 'existing' && $target->targets != null ) {
                array_push( $data['existing'], $target->count);
                array_push( $data['existing'], $target->targets);
                $counts['follow_ups'] = $target->count;
            }
            if( $target->target_type == 'account' && $target->targets != null ) {
                array_push( $data['account'], $target->count);
                array_push( $data['account'], $target->targets);
                $allCounts = $allCounts + $target->count;
                $allTargets = $allTargets + $target->targets;
            }
            if( $target->target_type == 'margin' && $target->targets != null ) {
                array_push( $data['margin'], $target->count);
                array_push( $data['margin'], $target->targets);
            }
            if( $target->target_type == 'mutual_funds' && $target->targets != null ) {
                array_push( $data['mutual_funds'], $target->count);
                array_push( $data['mutual_funds'], $target->targets);
            }
            if( $target->target_type == 'insurance' && $target->targets != null ) {
                array_push( $data['insurance'], $target->count);
                array_push( $data['insurance'], $target->targets);
            }
            if( $target->target_type == 'option_brains' && $target->targets != null ) {
                array_push( $data['option_brains'], $target->count);
                array_push( $data['option_brains'], $target->targets);
            }
        }

        if($allTargets > 0 ){
            $counts['target'] = $allTargets;
            $counts['achieved'] = $allCounts;
            $counts['fulfilled'] = round( ($allCounts / $allTargets) * 100, 2 );
            $counts['pending'] = $allTargets - $allCounts;
        } 
        else {
            $counts['target'] = $allTargets;
            $counts['achieved'] = $allCounts;
            $counts['fulfilled'] = 0;
            $counts['pending'] = 0; 
        }
        

        return response([
            'status' => 1, 
            'graph_stats' => $data, 
            'counts' => $counts,
            'message' => 'Stats loaded successfully',
        ]);

    }

    public function allServicesGraph() {

        $month = date('n');
        $year = date('Y');
        $targets = UserTargets::where('month', $month)
            ->where('year', $year)
            ->get();

        $data = [];
        
        $new = 0;
        $newTargets = 0; 
        $existing = 0; 
        $existingTargets = 0; 
        $account = 0; 
        $accountTargets = 0; 

        foreach( $targets as $target ) {

            if( $target->target_type == 'new' ) {
                $new += $target->count;
                $newTargets += $target->targets;
            }

            if( $target->target_type == 'existing' ) {
                $existing += $target->count;
                $existingTargets += $target->targets;
            }

            if( $target->target_type == 'account' ) {
                $account += $target->count;
                $accountTargets += $target->targets;
            }

        } 

        $titleArray = ['Type of Services', 'Actuals', 'Targets'];
        array_push( $data, $titleArray );
        
        $newArray = ['New Leads'];
        array_push( $newArray, $new );
        array_push( $newArray, $newTargets );
        array_push( $data, $newArray );

        $existingArray = ['Existing Leads'];
        array_push( $existingArray, $existing );
        array_push( $existingArray, $existingTargets );
        array_push( $data, $existingArray );
        
        $accountArray = ['Account'];
        array_push( $accountArray, $account );
        array_push( $accountArray, $accountTargets );
        array_push( $data, $accountArray );

        return response([
            'status' => 1, 
            'graphData' => $data,
            'month' => date('M')
        ]);

    }

    public function allServicesGraphNumbers() {

        $month = date('n');
        $year = date('Y');
        $targets = UserTargets::where('month', $month)
            ->where('year', $year)
            ->get();

        $data = [];
        
        $margin = 0; 
        $marginTargets = 0;
        $mututal_funds = 0; 
        $mututal_fundsTargets = 0; 
        $insurance = 0;
        $insuranceTargets = 0; 
        $third_party = 0; 
        $third_partyTargets = 0; 

        foreach( $targets as $target ) {

            if( $target->target_type == 'margin' ) {
                $margin += $target->count;
                $marginTargets += $target->targets;
            }

            if( $target->target_type == 'mututal_funds' ) {
                $mututal_funds += $target->count;
                $mututal_fundsTargets += $target->targets;
            }

            if( $target->target_type == 'insurance' ) {
                $insurance += $target->count;
                $insuranceTargets += $target->targets;
            }

            if( $target->target_type == 'option_brains' ) {
                $third_party += $target->count;
                $third_partyTargets += $target->targets;
            }

        } 

        $titleArray = ['Type of Services', 'Actuals', 'Targets'];
        array_push( $data, $titleArray );
        
        $marginArray = ['Margin'];
        array_push( $marginArray, $margin );
        array_push( $marginArray, $marginTargets );
        array_push( $data, $marginArray );

        $mututalArray = ['Mutual Funds'];
        array_push( $mututalArray, $mututal_funds );
        array_push( $mututalArray, $mututal_fundsTargets );
        array_push( $data, $mututalArray );

        $insuranceArray = ['Insurance'];
        array_push( $insuranceArray, $insurance );
        array_push( $insuranceArray, $insuranceTargets );
        array_push( $data, $insuranceArray );

        $thirdArray = ['Option Brains'];
        array_push( $thirdArray, $third_party );
        array_push( $thirdArray, $third_partyTargets );
        array_push( $data, $thirdArray );

        return response([
            'status' => 1, 
            'graphData' => $data,
            'month' => date('M')
        ]);

    }

    public function getStatsByType($type) {

        $month = date('n');
        $year = date('Y');
        $targets = UserTargets::join('users', 'users.id', '=', 'user_targets.user_id')
            ->where('month', $month)
            ->where('year', $year)
            ->where('target_type', $type)
            ->get();

        $data = [];
        $dataPartZero = ['Employee', 'Actuals'];
        array_push( $data, $dataPartZero );

        foreach($targets as $target) {
            $username = $target->first_name.' '.$target->last_name;
            $dataPart = [];
            array_push($dataPart, $username);
            array_push($dataPart, $target->count);
            array_push($data, $dataPart);
        }

        return response([
            'status' => 1, 
            'graphData' => $data
        ]);
        
    }

    public function updateStats() {

        $month = date('n');
        $year = date('Y');
 
        $storeStats = self::storeStats($month, $year);
        
    }

    public function clearExistingCount() {
        
        $leads = Leads::where(function($q) {
            $q->where('existingCount', '>', 0);
        })->get();

        foreach($leads as $lead) {
            $lead->existingCount = 0;
            $lead->save();
        }

    }

}