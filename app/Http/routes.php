<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $missed_calls = DB::table('missed_calls')->orderBy('created_at', 'desc')->get();
    return view('welcome', ["missed_calls" => $missed_calls]);
});

Route::post('/call/incoming', 'TaskRouterController@incomingCall');
Route::post('/call/enqueue', 'TaskRouterController@enqueueCall');
Route::post('/assignment', 'TaskRouterController@assignment');
Route::post('/events', 'TaskRouterController@events');