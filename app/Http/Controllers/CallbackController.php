<?php

namespace App\Http\Controllers;

use App\MissedCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TaskRouterException;
use Twilio\Rest\Client;

/**
 * Class CallbackController Handles callbacks
 * @package App\Http\Controllers
 */
class CallbackController extends Controller
{
    /**
     * Callback endpoint for Task assignments
     */
    public function assignTask()
    {
        $deQueueModel = new \stdClass;
        $deQueueModel->instruction = "dequeue";
        $deQueueModel->post_work_activity_sid = config('services.twilio')['postWorkActivitySid'];

        $dequeueInstructionJson = json_encode($deQueueModel);

        return response($dequeueInstructionJson)->header('Content-Type', 'application/json');
    }

    /**
     * Events callback for missed calls
     */
    public function handleEvent(Request $request, Client $twilioClient)
    {
        $desirableEvents = config('services.twilio')['desirableEvents'];
        $eventTypeName = $request->input("EventType");
        if (in_array($eventTypeName, $desirableEvents)) {
            $task = $this->parseTaskAttributes($request);
            if (!empty($task)) {
                $this->addMissingCall($task);
                $message = config('services.twilio')["leave_message"];
                return $this->redirectToVoiceMail($twilioClient, $task->call_sid, $message);
            }
        }
    }

    protected function parseTaskAttributes($request)
    {
        $taskAttrJson = $request->input("TaskAttributes");
        return json_decode($taskAttrJson);
    }

    protected function addMissingCall($task)
    {
        $missedCall = new MissedCall(["selected_product" => $task->selected_product, "phone_number" => $task->from]);
        $missedCall->save();
        Log::info("New missed call added: $missedCall");
    }

    protected function redirectToVoiceMail($twilioClient, $callSid, $message)
    {
        $missedCallsEmail = config('services.twilio')['missedCallsEmail']
        or die("MISSED_CALLS_EMAIL_ADDRESS is not set in the environment");

        $call = $twilioClient->calls->getContext($callSid);
        if(!$call)
            throw new TaskRouterException("The specified call does not exist");

        $encodedMsg = urlencode($message);
        $twimletUrl = "http://twimlets.com/voicemail?Email=$missedCallsEmail&Message=$encodedMsg";
        $call->update(["url" => $twimletUrl, "method" => "POST"]);
    }
}