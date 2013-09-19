<?php

class TreasureData_API_GetTableListTest extends PHPUnit_Framework_TestCase
{
    public function testGetTableListAPI()
    {
        $api    = new TreasureData_API();
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "api",
                "table_list.json"
        )));

        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream($fixture)
            )));

        $api->setDriver($stub);
        $result = $api->getTableList("db0");

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();

        $this->assertInstanceof('TreasureData_API_Message_TableList', $message);
        /** @var TreasureData_API_Message_TableList $message */

        $this->assertEquals("db0", $message->getDatabase());

        $i = 0;
        $expected = json_decode($fixture, true);
        foreach ($message->getTables() as $table) {
            /** @var TreasureData_API_Message_Table $table */

            $this->assertInstanceof("TreasureData_API_Message_Table", $table);
            $this->assertEquals($expected['tables'][$i]['count'], $table->getCount());
            $this->assertEquals($expected['tables'][$i]['name'], $table->getName());
            $i++;
        }
    }

}