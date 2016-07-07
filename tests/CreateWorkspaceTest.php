<?php

class CreateWorkspaceTest extends TestCase
{

    public function testWorkspaceJsonWithExpectedVariables()
    {
        //given
        $twilioClient = $this->getMockBuilder("Twilio\Rest\Client")
            ->setConstructorArgs(
                [
                    "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
                    "WSXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
                ]
            )
            ->getMock();
        $createWorkspaceCmd = Mockery::mock(
            "App\Console\Commands\CreateWorkspace", $twilioClient
        );
        $createWorkspaceCmd->shouldReceive("argument")
            ->once()
            ->andReturn(
                [
                    "host" => "hostname",
                    "bob_phone" => "+54345345345",
                    "alice_phone" => "+4535344"
                ]
            );

        $fileContent = File::get("resources/workspace.json");
        $interpolatedContent
            = sprintfn($fileContent, $createWorkspaceCmd->argument());
        $jsonData = json_decode($interpolatedContent);

        $createWorkspaceCmd->shouldReceive('createWorkspaceConfig')
            ->once()
            ->andReturn($jsonData);

        $jsonContent = $createWorkspaceCmd->createWorkspaceConfig();
        $this->assertFalse(empty($jsonContent));
        $this->assertEquals("hostname/events", $jsonContent->event_callback);
        $this->assertEquals(
            "+54345345345", $jsonContent->workers[0]->attributes->contact_uri
        );
        $this->assertEquals(
            "+4535344", $jsonContent->workers[1]->attributes->contact_uri
        );
    }
    
}
