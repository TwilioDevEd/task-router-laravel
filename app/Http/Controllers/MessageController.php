<?php

namespace App\Http\Controllers;

use App\Exceptions\TaskRouterException;
use Illuminate\Http\Request;
use App\TaskRouter\WorkspaceFacade;
use Twilio\Twiml;

class MessageController extends Controller
{

    public function handleIncomingMessage(
        Request $request, WorkspaceFacade $workspace
    ) {
        $cmd = strtolower($request->input("Body"));
        $fromNumber = $request->input("From");
        $newWorkerStatus = ($cmd === "off") ? "Offline" : "Idle";

        $response = new Twiml();

        try {
            $worker = $this->getWorkerByPhone($fromNumber, $workspace);
            $this->updateWorkerStatus($worker, $newWorkerStatus, $workspace);

            $response->sms("Your status has changed to {$newWorkerStatus}");

        } catch (TaskRouterException $e) {
            $response->sms($e->getMessage());
        }

        return response($response)->header('Content-Type', 'text/xml');
    }

    function updateWorkerStatus($worker, $status, $workspace)
    {
        $wantedActivity = $workspace->findActivityByName($status);
        $workspace->updateWorkerActivity($worker, $wantedActivity->sid);
    }

    protected function getWorkerByPhone($phone, $workspace)
    {
        $phoneToWorkerStr = config('services.twilio')['phoneToWorker'];
        parse_str($phoneToWorkerStr, $phoneToWorkerArray);
        if (empty($phoneToWorkerArray[$phone])) {
            throw new TaskRouterException("You are not a valid worker");
        }
        return $workspace->findWorkerBySid($phoneToWorkerArray[$phone]);
    }
}