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

/**
 * Main view
 */
Route::get('/', function () {
    $missed_calls = App\MissedCall::orderBy('name', 'desc')->get();
    return view('welcome', ["missed_calls" => $missed_calls]);
});

/**
 * Endpoints
 */
Route::group(
    ['middleware' => ['web']], function () {
        Route::post(
            '/call/incoming',
            ['uses' => 'IncomingCallController@respondToUser',
                'as' => 'call.incoming']
        );
        Route::post(
            '/call/enqueue',
            ['uses' => 'EnqueueCallController@enqueueCall',
                'as' => 'call.enqueue']
        );
        Route::post(
            '/assignment',
            ['uses' => 'CallbackController@assignTask',
                'as' => 'assignment']
        );
        Route::get(
            '/events',
            ['uses' => 'CallbackController@handleEvent',
                'as' => 'events']
        );
    }
);