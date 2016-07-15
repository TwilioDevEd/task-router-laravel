<?php


class EnqueueCallControllerTest extends TestCase
{

    public function testSelectedProductProgrammableSMS()
    {
        $response = $this->call(
            'POST',
            '/call/enqueue',
            ["Digits" => 1]
        );

        $twilioXmlResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
            '{"selected_product":"ProgrammableSMS"}',
            strval($twilioXmlResponse->Enqueue->Task)
        );
    }

    public function testSelectedProductProgrammableVoice()
    {
        $response = $this->call(
            'POST',
            '/call/enqueue',
            ["Digits" => 2]
        );

        $twilioXmlResponse = new SimpleXMLElement($response->getContent());

        $this->assertEquals(
            '{"selected_product":"ProgrammableVoice"}',
            strval($twilioXmlResponse->Enqueue->Task)
        );
    }

}
