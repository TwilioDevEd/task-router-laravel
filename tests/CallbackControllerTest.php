<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;

class CallbackControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testAssignTask()
    {
        $fakePostWorkActivitySid = env("POST_WORK_ACTIVITY_SID");
        $response = $this->call('POST', '/assignment');
        $twilioJsonResponse = json_decode($response->getContent());

        $this->assertEquals($twilioJsonResponse->instruction, "dequeue");
        $this->assertEquals(
            $twilioJsonResponse->post_work_activity_sid, $fakePostWorkActivitySid
        );
    }

    /**
     * Tests CallbackController@handleEvent
     *
     * @params $taskAttributes mixed containing the attributes of the task send by
     * Twilio
     *
     * @dataProvider providerTaskAttributesJson
     */
    public function testHandleEvent($taskAttributes)
    {
        $leaveMsg = config('services.twilio')["leaveMessage"];
        $desirableEvents = config('services.twilio')['desirableEvents'];
        foreach ($desirableEvents as $desirableEvent) {
            $response = $this->call(
                'POST',
                '/events',
                [
                    "EventType" => $desirableEvent,
                    "TaskAttributes" => $taskAttributes
                ]
            );
            $this->assertResponseOk();
            $this->assertEquals('Leaving message', $response->getContent());
        }
    }

    public function providerTaskAttributesJson()
    {
        $task = new \stdClass();
        $task->selected_product = "ProgrammableSMS";
        $task->from = "+1234567890";
        $task->call_sid = "00001111001";
        $programmableSMSJson = json_encode($task);
        return $programmableSMSJson;
    }

}