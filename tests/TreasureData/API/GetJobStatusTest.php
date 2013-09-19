<?php

class TreasureData_API_GetJobStatusTest extends PHPUnit_Framework_TestCase
{
    public function testGetJobStatusAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "job_status.json"
        )));

        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
        )));

        $api->setDriver($stub);
        $result = $api->getJobStatus(860329);

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();
        /** @var TreasureData_API_Message_JobStatus $message */

        $this->assertInstanceof('TreasureData_API_Message_JobStatus', $message);

        $this->assertEquals("860329", $message->getJobId());
        $this->assertEquals("success", $message->getStatus());
        $this->assertInstanceof("DateTime", $message->getCreatedAt());
        $this->assertInstanceof("DateTime", $message->getStartAt());
        $this->assertInstanceof("DateTime", $message->getEndAt());
    }

}