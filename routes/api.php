<?php

use Illuminate\Http\Request;
$api = app('Dingo\Api\Routing\Router');

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$api->version('v2',function($api)
{
    // Facebook Register
    $api->post('fb-register','App\Http\Controllers\UserController@register');
    
    //Facebook Login
    $api->post('login','App\Http\Controllers\UserController@login');
    
    //Facebook View Data
    $api->get('fb-view','App\Http\Controllers\UserController@index');
    
    //Facebook verification Token
    $api->get('verification/{token}','App\Http\Controllers\UserController@userVerfication');
    
    //Facebook Get User Data
    $api->get('user', 'App\Http\Controllers\UserController@getUser');
    
    //update fb user data
    $api->put('fb-update/{update}','App\Http\Controllers\UserController@update');
});
    
    //logout user
    $api->version('v2',['middleware' => ['jwt.auth']],function($api)
    {
        $api->get('logout','App\Http\Controllers\UserController@logout');
    });

    //Get Tokken
    $api->version('v2',function($api)
    {
        $api->get('gettokken/{token}','App\Http\Controllers\UserController@getTokken');
    });
    
    //Website Relationship
    
    $api->version('v2',function($api)
    {
        $api->resource('website','App\Http\Controllers\WebsiteController');
    });