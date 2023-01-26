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
                $value->phone,
                strtoupper($value->user_type)
			]);   
        }
        return $result;
	}
}