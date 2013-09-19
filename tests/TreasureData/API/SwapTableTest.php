<?php

class TreasureData_API_SwapTableTest extends PHPUnit_Framework_TestCase
{
    public function testGetDatabaseListAPI()
    {
        $api    = new TreasureData_API();

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "swap_table.json"
            )));
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
            )));

        $api->setDriver($stub);
        $result = $api->swapTable("db1", "tbl1", "tbl2");

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();
        /** @var TreasureData_API_Message_SwapTable $message */
        $this->assertInstanceof('TreasureData_API_Message_SwapTable', $message);

        $this->assertEquals("db1", $message->getDatabase());
        $this->assertEquals("tbl1", $message->getTable1());
        $this->assertEquals("tbl2", $message->getTable2());
    }

}