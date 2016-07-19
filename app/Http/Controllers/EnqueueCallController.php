<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Twiml;


/**
 * Class EnqueueCallController
 *
 * @package App\Http\Controllers
 */
class EnqueueCallController extends Controller
{

    public function enqueueCall(Request $request)
    {
        $workflowSid = config('services.twilio')['workflowSid']
        or die("WORKFLOW_SID is not set in the system environment");

        $selectProductInstruction = new \StdClass();
        $selectProductInstruction->selected_product
            = $this->_getSelectedProduct($request);

        $response = new Twiml();
        $enqueue = $response->enqueue(['workflowSid' => $workflowSid]);
        $enqueue->task(json_encode($selectProductInstruction));

        return response($response)->header('Content-Type', 'text/xml');
    }

    /**
     * Gets the wanted product upon the user's input
     *
     * @param $request Request of the user
     *
     * @return string selected product: "ProgrammableSMS" or "ProgrammableVoice"
     */
    private function _getSelectedProduct($request)
    {
        return $request->input("Digits") == 1
            ? "ProgrammableSMS"
            : "ProgrammableVoice";
    }
}