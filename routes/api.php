<?php

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


});