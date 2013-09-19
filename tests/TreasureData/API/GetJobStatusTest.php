<?php

class TreasureData_API_GetJobStatusTest extends PHPUnit_Framework_TestCase
{
    public function testGetJobStatusAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream('{
  "job_id":"860329",
  "status":"success",
  "created_at":"2012-09-17 21:00:00 UTC",
  "start_at":"2012-09-17 21:00:01 UTC",
  "end_at":"2012-09-17 21:00:52 UTC"
}'
                ))));

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