<?php

class TreasureData_API_ShowJobTest extends PHPUnit_Framework_TestCase
{
    public function testShowJobAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "show_job.json"
            )));
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
            )));

        $api->setDriver($stub);
        $result = $api->showJob("12345");

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();
        /** @var TreasureData_API_Message_JobInformation $message */
        $this->assertInstanceof('TreasureData_API_Message_JobInformation', $message);

        $this->assertEquals("12345", $message->getJobId());
        $this->assertEquals("SELECT * FROM ACCESS", $message->getQuery());
        $this->assertEquals("hive", $message->getType());
        $this->assertEquals("http://console.treasure-data.com/jobs/12345", $message->getUrl());
        $this->assertInstanceof("DateTime", $message->getCreatedAt());
        $this->assertInstanceof("DateTime", $message->getUpdatedAt());

        $debug = $message->getDebug();
        /** @var TreasureData_API_Message_JobInformationDebug $debug */
        $this->assertInstanceof('TreasureData_API_Message_JobInformationDebug', $debug);
        $this->assertEquals("...", $debug->getCmdout());
        $this->assertEquals("...", $debug->getStderr());
    }

}