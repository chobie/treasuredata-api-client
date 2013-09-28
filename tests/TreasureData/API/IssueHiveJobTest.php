<?php

class TreasureData_API_IssueHiveJobTest extends PHPUnit_Framework_TestCase
{
    public function testIssueHiveJobAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "issue_hive_job.json"
            )));
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
            )));

        $api->setDriver($stub);
        $result = $api->issueHiveQuery("www_access", "select v['code'] as code, count(1) as cnt from www_access group by v['code']");

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();
        /** @var TreasureData_API_Message_IssueJob $message */
        $this->assertInstanceof('TreasureData_API_Message_IssueJob', $message);

        $this->assertEquals("www_access", $message->getDatabase());
        $this->assertEquals("hive", $message->getType());
        $this->assertEquals("http://console.treasure-data.com/will-be-ready", $message->getUrl());
    }

}