<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
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
        $workflowSid = env("WORKFLOW_SID");
        $response = new Twiml();
        $enqueue = $response->enqueue(['workflowSid' => $workflowSid]);
        $selectedProduct = $this->_getSelectedProduct($request);
        $enqueue->task("{\"selected_product\": \"$selectedProduct\"}");
        return response($response)->header('Content-Type', 'text/xml');
    }

    /**
     * Get the selected product based on user input
     * @param $request
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