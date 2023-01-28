<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserTargets;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadActivity;

class StatsController extends Controller
{

    public function storeStats($month, $year) {

        $users = User::where('user_target_type', 'user')->get();
       
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
                'target_type' => 'third_party',
                'targets' => $third_party
            ];
            $addThirdPartyData = UserTargets::updateOrInsert([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'target_type' => 'third_party'
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
            array_push(
                $result['data'],
                [
                    date('M', strtotime($target->month)).' - '.$target->year,
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
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'new',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
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
        })->get();

        $existingLeads = $leadAdded->count();

        $baseData = [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'existing',
        ];

        $addExistingData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
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
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'account',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'account',
            'count' => $newAccount
        ]);

    }
    
    public function saveMFCount($user, $month, $year) {

        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
            $q->where('account_category', 'mutual_funds');
            $q->whereNull('third_party');
        })->get();

        $newAccount = $leadAdded->count();

        $baseData = [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'mutual_funds',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'mutual_funds',
            'count' => $newAccount
        ]);
        
    }

    public function saveInsuranceCount($user, $month, $year) {

        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
            $q->where('account_category', 'insurance');
            $q->whereNull('third_party');
        })->get();

        $newAccount = $leadAdded->count();

        $baseData = [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'insurance',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'insurance',
            'count' => $newAccount
        ]);

    }
    
    public function saveMarginCount($user, $month, $year) {

        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
            $q->where('account_category', 'margin');
            $q->whereNull('third_party');
        })->get();

        $newAccount = $leadAdded->count();

        $baseData = [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'margin',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'margin',
            'count' => $newAccount
        ]);

    }
    
    public function saveThirdPartyCount($user, $month, $year) {
    
        $leadAdded = Lead::where(function($q) use($month, $year, $user) {
            $q->whereMonth('created_at', $month);
            $q->whereYear('created_at', $year);
            $q->where('lead_owner', $user->id);
            $q->whereNotNull('third_party');
        })->get();

        $newAccount = $leadAdded->count();

        $baseData = [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'third_party',
        ];

        $addNewData = UserTargets::updateOrInsert($baseData, [
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'target_type' => 'third_party',
            'count' => $newAccount
        ]);        

    }
    

}