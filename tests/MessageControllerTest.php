<?php

class MessageControllerTest extends TestCase
{

    /**
     * @param $postParams Post params
     * @dataProvider providerPostParams
     */
    public function testHandleIncomingMessage($body, $phone, $expected)
    {
        $response = $this->call(
            'POST',
            '/message/incoming',
            ["Body" => $body, "From" => $phone]
        );
    }

    public function providerPostParams()
    {
        return [
            ["off", "+1233456678", "Offline"],
            ["on", "+1233456678", "Idle"]
        ];
    }

}
