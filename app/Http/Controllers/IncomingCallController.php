<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Twilio\Twiml;

/**
 * Class IncomingCallController
 *
 * @package App\Http\Controllers
 */
class IncomingCallController extends Controller
{

    public function respondToUser()
    {
        $response = new Twiml();

        $params = array();
        $params['action'] = '/call/enqueue';
        $params['numDigits'] = 1;
        $params['timeout'] = 10;
        $params['method'] = "POST";

        $params = $response->gather($params);
        $params->say(
            'For Programmable SMS, press one. For Voice, press any other key.'
        );

        return response($response)->header('Content-Type', 'text/xml');
    }
}