<?php

namespace App\Http\Controllers;

use App\Exceptions\TaskRouterException;
use App\MissedCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

/**
 * Class CallbackController Handles callbacks
 *
 * @package App\Http\Controllers
 */
class CallbackController extends Controller
{
    /**
     * Callback endpoint for Task assignments
     */
    public function assignTask()
    {
        $dequeueInstructionModel = new \stdClass;
        $dequeueInstructionModel->instruction = "dequeue";
        $dequeueInstructionModel->post_work_activity_sid
            = config('services.twilio')['postWorkActivitySid'];

        $dequeueInstructionJson = json_encode($dequeueInstructionModel);

        return response($dequeueInstructionJson)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Events callback for missed calls
     *
     * @param $request Request with the input data
     * @param $twilioClient Client of the Twilio Rest Api
     */
    public function handleEvent(Request $request, Client $twilioClient)
    {
        $missedCallEvents = config('services.twilio')['missedCallEvents'];

        $eventTypeName = $request->input("EventType");

        if (in_array($eventTypeName, $missedCallEvents)) {
            $taskAttr = $this->parseAttributes("TaskAttributes", $request);
            if (!empty($taskAttr)) {
                $this->addMissingCall($taskAttr);

                $message = config('services.twilio')["leaveMessage"];
                return $this->redirectToVoiceMail(
                    $twilioClient, $taskAttr->call_sid, $message
                );
            }
        } else if ('worker.activity.update' === $eventTypeName) {
            $workerActivityName = $request->input("WorkerActivityName");
            if ($workerActivityName === "Offline") {
                $workerAttr = $this->parseAttributes("WorkerAttributes", $request);
                $this->notifyOfflineStatusToWorker(
                    $workerAttr->contact_uri, $twilioClient
                );
            }
        }
    }

    protected function parseAttributes($name, $request)
    {
        $attrJson = $request->input($name);
        return json_decode($attrJson);
    }

    protected function addMissingCall($task)
    {
        $missedCall = new MissedCall(
            [
            "selected_product" => $task->selected_product,
            "phone_number" => $task->from
            ]
        );
        $missedCall->save();
        Log::info("New missed call added: $missedCall");
    }

    protected function redirectToVoiceMail($twilioClient, $callSid, $message)
    {
        $missedCallsEmail = config('services.twilio')['missedCallsEmail']
            or die("MISSED_CALLS_EMAIL_ADDRESS is not set in the environment");

        $call = $twilioClient->calls->getContext($callSid);
        if (!$call) {
            throw new TaskRouterException("The specified call does not exist");
        }

        $encodedMsg = urlencode($message);
        $twimletUrl = "http://twimlets.com/voicemail?Email=$missedCallsEmail" .
            "&Message=$encodedMsg";
        $call->update(["url" => $twimletUrl, "method" => "POST"]);
    }

    protected function notifyOfflineStatusToWorker($workerPhone, $twilioClient)
    {
        $twilioNumber = config('services.twilio')['number']
        or die("TWILIO_NUMBER is not set in the system environment");

        $params = [
            "from" => $twilioNumber,
            "body" => config('services.twilio')["offlineMessage"]
        ];

        $twilioClient->account->messages->create(
            $workerPhone,
            $params
        );
    }

}
