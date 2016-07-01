<?php

namespace App\Http\Controllers;

use App\MissedCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $dequeueModel = new \stdClass;
        $dequeueModel->instruction = "dequeue";
        $dequeueModel->post_work_activity_sid = config('services.twilio')['postWorkActivitySid'];

        $dequeueInstructionJson = json_encode($dequeueModel);

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
            $task = $this->_parseTaskAttributes($request);
            if (!empty($task)) {
                $this->_addMissingCall($task);
                $message = config('services.twilio')["leave_message"];
                return $this->_leaveMessage($twilioClient, $task->call_sid, $message);
            }
        }

    }

    private function _parseTaskAttributes($request)
    {
        $taskAttrJson = $request->input("TaskAttributes");
        return json_decode($taskAttrJson);
    }

    private function _addMissingCall($task)
    {
        $missedCall = new MissedCall(["selectedProduct" => $task->selected_product, "phoneNumber" => $task->from]);
        $missedCall->save();
        Log::info("New missed call added: $missedCall");
    }

    private function _leaveMessage($twilioClient, $callSid, $message)
    {
        $missedCallsEmail = config('services.twilio')['missedCallsEmail'];
        $message = urlencode($message);
        $twimletUrl = "http://twimlets.com/voicemail?Email=$missedCallsEmail&Message=$message";
        $twilioClient->account->update(["Url" => $twimletUrl, "Method" => "POST"]);
    }
}