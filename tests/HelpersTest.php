<?php


class HelpersTest extends TestCase
{

    public function testReplaceExistingEnvVariable()
    {
        $content = "VAR1=VALUE1\nVAR2=VALUE2\nVAR3=VALUE3";
        $expected = "VAR1=VALUE1\nVAR2=NEWVALUE2\nVAR3=VALUE3";
        $nonExpected = "VAR1=VALUE1\nVAR2=XWWWW\nVAR3=VALUE3";
        $result = preg_replace("/VAR2=(.*)/", "VAR2=NEWVALUE2", $content);
        $this->assertEquals($expected, $result, "The VAR was not replaced");
        $this->assertNotEquals(
            $nonExpected, $result, "The assertion is not working for the replacement"
        );
    }

    public function testIfEnvVarDoesntExistAddIt()
    {
        $content = "VAR1=VALUE1\nVAR2=VALUE2\nVAR3=VALUE3";
        $expected = "VAR1=VALUE1\nVAR2=VALUE2\nVAR3=VALUE3\nVAR4=NEWVALUE4\n";
        $resultWithAdition = addOrReplaceEnvVar("VAR4", "NEWVALUE4", $content);
        $this->assertEquals($expected, $resultWithAdition, "The VAR4 was not added");
        $resultWithUpdate = addOrReplaceEnvVar(
            "VAR4", "WAXXXXXXXXXXX", $resultWithAdition
        );
        $expectedWithUpdate
            = "VAR1=VALUE1\nVAR2=VALUE2\nVAR3=VALUE3\nVAR4=WAXXXXXXXXXXX\n";
        $this->assertEquals(
            $expectedWithUpdate, $resultWithUpdate,
            "The update to VAR4 was not applied"
        );
    }

    public function testSerializeAndDeserializeWorkersPhoneHash()
    {
        $workersPhone = [
            "+1234567890" => "Bob",
            "+1098765432" => "Alice"
        ];
        $serializedArray = http_build_query($workersPhone);
        parse_str($serializedArray, $workersReceived);
        $this->assertEquals(
            $workersReceived["+1234567890"], "Bob", "Bob's phone is not the expected"
        );
        $this->assertEquals(
            $workersReceived["+1098765432"], "Alice",
            "Alice's phone is not the expected"
        );
    }

    public function testFormatPhoneNumberToUSInternational()
    {
        $this->assertEquals(
            "+1 415-723-4000", formatPhoneNumberToUSInternational("+14157234000"),
            "The international format for the numbers is not the expected"
        );
    }

}