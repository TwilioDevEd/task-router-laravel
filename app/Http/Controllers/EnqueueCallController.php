<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Exceptions\TwilioException;
use Twilio\Twiml;


/**
 * Class EnqueueCallController
 * @package App\Http\Controllers
 */
class EnqueueCallController extends Controller
{

    public function enqueueCall(Request $request)
    {
        $workflowSid = config('services.twilio')['workflowSid'];
        $response = new Twiml();
        $enqueue = $response->enqueue(['workflowSid' => $workflowSid]);
        $selectedProduct = $this->_getSelectedProduct($request);
        $enqueue->task("{\"selected_product\": \"$selectedProduct\"}");
        return response($response)->header('Content-Type', 'text/xml');
    }

    /**
     * @param $request User Request
     * @return selected product based on user input
     */
    private function _getSelectedProduct($request)
    {
        $selectedOption = $request->input("Digits");
        if (empty($selectedOption))
        {
            throw new TwilioException("You have not specified a valid option");
        }
        return $selectedOption == 1 ? "ProgrammableSMS" : "ProgrammableVoice";
    }
}