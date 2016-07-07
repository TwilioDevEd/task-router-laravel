<?php

use App\Http\Controllers\MessageController;

class MessageControllerTest extends TestCase
{

    /**
     * Tests MessageController@handleIncomingMessage
     *
     * @param $postParams Post params
     *
     * @dataProvider providerPostParams
     */
    public function testHandleIncomingMessage($body, $phone, $expectedStatus)
    {
        $messageController = new MessageController();

        $workspaceMock = Mockery::mock(
            "App\TaskRouter\WorkspaceFacade"
        );

        $workspaceMock->shouldReceive("updateWorkerActivity")
            ->once();

        $workspaceMock->shouldReceive("findWorkerBySid")
            ->once()
            ->andReturn(new \stdClass());

        $activityMock = new \stdClass();
        $activityMock->sid = "WXASSSSS";
        $workspaceMock->shouldReceive("findActivityByName")
            ->once()
            ->andReturn($activityMock);

        $requestMock = Mockery::mock(
            "Illuminate\Http\Request"
        );

        $requestMock->shouldReceive("input")
            ->with("Body")
            ->once()
            ->andReturn($body);

        $requestMock->shouldReceive("input")
            ->with("From")
            ->once()
            ->andReturn($phone);

        $response = $messageController->handleIncomingMessage(
            $requestMock,
            $workspaceMock
        );

        $twilioXmlResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
            "Your status has changed to $expectedStatus",
            strval($twilioXmlResponse->Sms)
        );
    }

    public function providerPostParams()
    {
        return [
            ["off", "+123456789", "Offline"],
            ["on", "+123456788", "Idle"]
        ];
    }

}
