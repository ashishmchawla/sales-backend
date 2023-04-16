<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class ApiAuthController extends Controller 
{
    
    // User Registration
    public function register(Request $request) 
    {
        $data = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);
        $token = $user->createToken('TriventureToken')->accessToken;

        return response([ 'user' => $user, 'token' => $token, 'status' => 1]);

    }

    // User Login
    public function login(Request $request) 
    {

        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if( !auth()->attempt($data) ) {
            return response(['status' => 0, 'error_message' => 'Incorrect Details, please try again']);
        }
        
        $token = auth()->user()->createToken('TriventureToken')->accessToken;

        return response([
            'status' => 1, 
            'user' => auth()->user(),
            'token' => $token
        ]);

    }
    
    public function adminLogin(Request $request) 
    {

        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        
        $checkIfAdmin = User::where('email', $request->email)->where('user_type', 'admin')->first();
        if( !auth()->attempt($data) ) {
            return response(['status' => 0, 'error_message' => 'Incorrect Details, please try again']);
        }
        if( $checkIfAdmin ) {
            
            $token = auth()->user()->createToken('TriventureToken')->accessToken;
            return response([
                'status' => 1, 
                'user' => auth()->user(),
                'token' => $token
            ]);

        } 
        else {
            return response(['status' => 0, 'error_message' => 'Incorrect Details, please try again']);
        }
    }
 
    // User Forgot Password
    public function forgotPassword(Request $request) {

		$user_id = Auth::user()->id;
        $user = User::find($user_id);
        $user->password = bcrypt($request->password);

        if( $user->save() ) {
            $token = auth()->user()->createToken('TriventureToken')->accessToken;
            return response([
                'status' => 1, 
                'message' => 'Password reset successfully',
                'token' => $token,
                'user' => $user
            ]);
        }
        else {
            return response([
                'status' => 0,
                'error_message' => 'Something went wrong, please try again'
            ]);
        }

    }

    public function verifyEmail(Request $request) {

        $userExists = User::where('email', $request->email)->first();

        if( $userExists != null) {
            $token = $userExists->createToken('TriventureToken')->accessToken;
            return response([
                'status' => 1, 
                'message' => 'User exists',
                'token' => $token,
                'user' => $userExists
            ]);
        }
        else {
            return response([
                'status' => 0,
                'error_message' => 'No user found with this email.'
            ]);
        }

    }

    public function getUsers() {

        $users = User::all();
        return response([
            'users' => $users
        ]);
    
    }

    public function tokenLogin () {
        return Auth::user(); 
    }
}