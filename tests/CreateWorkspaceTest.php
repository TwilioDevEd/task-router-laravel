<?php

use App\Console\Commands\CreateWorkspace;
use App\TwilioAppSettings;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;

class CreateWorkspaceTest extends TestCase
{

    public function testWorkspaceJsonWithExpectedVariables()
    {
        //given
        $createWorkspaceCmd = Mockery::mock("App\Console\Commands\CreateWorkspace[argument]");
        $createWorkspaceCmd->shouldReceive("argument")
            ->once()
            ->andReturn(["host" => "hostname", "bob_phone" => "+54345345345", "alice_phone" => "+4535344"]);
        $jsonContent = $createWorkspaceCmd->createWorkspaceConfig();
        $this->assertFalse(empty($jsonContent));
        $this->assertEquals("hostname/events", $jsonContent->event_callback);
        $this->assertEquals("+54345345345", $jsonContent->workers[0]->attributes->contact_uri);
        $this->assertEquals("+4535344", $jsonContent->workers[1]->attributes->contact_uri);
    }
}
