<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmployeeController extends Controller
{
    
    public function allUsers(){
        
        $users = User::all();
        $result['data']=[];

        foreach ($users as $value) {
        
			array_push($result['data'],[
				$value->id,
				$value->first_name. ' '.$value->last_name,
				$value->email,
                strtoupper($value->user_type)
			]);   
        }
        return $result;
	}

    public function getUserDetails($user_id) {

        $user = User::find($user_id);

        if ($user) {
            return response([
                'status' => 1,
                'details' => $user,
                'message' => 'User details fetched'
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