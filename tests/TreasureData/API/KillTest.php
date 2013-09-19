<?php

class TreasureData_API_KillTest extends PHPUnit_Framework_TestCase
{
    public function testShowJobAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "kill.json"
            )));
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
            )));

        $api->setDriver($stub);
        $result = $api->killJob("12345");

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();
        /** @var TreasureData_API_Message_Kill $message */
        $this->assertInstanceof('TreasureData_API_Message_Kill', $message);

        $this->assertEquals("12345",   $message->getJobId());
        $this->assertEquals("running", $message->getFormerStatus());
    }

}