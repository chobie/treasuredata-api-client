<?php

class TreasureData_API_GetDatabaseListTest extends PHPUnit_Framework_TestCase
{
    public function testGetDatabaseListAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "databases.json"
            )));
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
            )));

        $api->setDriver($stub);
        $result = $api->getDatabaseList();

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();

        $this->assertInstanceof('TreasureData_API_Message_Databases', $message);
        $databases = $message->getDatabases();

        $this->assertEquals("db0", $databases[0]->getName());
        $this->assertEquals("db1", $databases[1]->getName());
    }

}