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
Route::get(
    '/', function () {
        $missed_calls = App\MissedCall::orderBy('created_at', 'desc')->get();

        $twilioNumber = config('services.twilio')['number']
          or die("TWILIO_NUMBER is not set in the system environment");

        return view(
            'welcome', [
            "missed_calls" => $missed_calls,
            "twilioNumber" => $twilioNumber
            ]
        );
    }
);

/**
 * Endpoints
 */
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
Route::post(
    '/events',
    ['uses' => 'CallbackController@handleEvent',
        'as' => 'events']
);
Route::post(
    '/message/incoming',
    ['uses' => 'MessageController@handleIncomingMessage',
        'as' => 'messages']
);
