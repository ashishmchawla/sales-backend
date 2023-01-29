<?php

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', 'Auth\ApiAuthController@register');
Route::post('/login', 'Auth\ApiAuthController@login');
Route::post('/verifyEmail', 'Auth\ApiAuthController@verifyEmail');
Route::get('/getUsers', 'Auth\ApiAuthController@getUsers');

Route::group(['middleware' => 'auth:api'], function () {

    // Reset Password
    Route::post('/forgotPassword', 'Auth\ApiAuthController@forgotPassword');

    // Lead Routes
    Route::post('/leads/createLead', 'LeadController@createLead');
    Route::post('/leads/editLead', 'LeadController@editLead');
    Route::post('/leads/search', 'LeadController@searchLeadByName');
    Route::get('/leads/{lead_id}', 'LeadController@getLeadDetails');
    Route::get('/leads', 'LeadController@getLeads');

    // Lead Activity Routes
    Route::post('/lead_activity/create', 'LeadActivityController@createActivity');
    Route::put('/lead_activity/edit/{activity_id}', 'LeadActivityController@editActivity');
    Route::delete('/lead_activity/{activity_id}', 'LeadActivityController@deleteActivity');

	// Register Token
	Route::post('/registerUserDevice', 'PushNotificationController@registerUserDevice');
	Route::post('/sendNotification', 'PushNotificationController@sendPushNotification');

    // App Stats
    Route::get('/appStats/{user_id}', 'StatsController@getUserStats');

    // Dashboard APIs
	Route::get('tokenLogin', 'Auth\ApiAuthController@tokenLogin');
    Route::get('allServicesGraph', 'StatsController@allServicesGraph');
    Route::get('getStatsByType/{type}', 'StatsController@getStatsByType');

    // Users
    Route::get('/allUsers', 'EmployeeController@allUsers');
    Route::get('/user_details/{user_id}', 'EmployeeController@getUserDetails');
    Route::post('/set_user_targets', 'StatsController@setUserTargets');
    Route::get('/user_targets_table/{user_id}', 'StatsController@getUserTargetTable');

    // Leads
    Route::get('/allLeads', 'LeadController@allLeads');
    Route::get('/lead_details/{lead_id}', 'LeadController@getLeadDetailsForAdmin');
    Route::get('/lead_activities/{lead_id}', 'LeadController@getLeadActivities');

});