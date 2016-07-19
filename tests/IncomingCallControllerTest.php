<?php

class IncomingCallControllerTest extends TestCase
{

    public function testRespondeIncomingCallToUser()
    {
        $response = $this->call(
            'POST',
            '/call/incoming'
        );

        $twilioXmlResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
            'For Programmable SMS, press one. For Voice, press any other key.',
            strval($twilioXmlResponse->Gather->Say)
        );
    }

}
