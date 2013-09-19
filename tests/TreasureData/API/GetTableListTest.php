<?php

class TreasureData_API_GetTableListTest extends PHPUnit_Framework_TestCase
{
    public function testGetTableListAPI()
    {
        $api    = new TreasureData_API();
        $stub = $this->getMockForAbstractClass('TreasureData_API_Driver');
        $stub->expects($this->any())
            ->method('request')
            ->will($this->returnValue(new TreasureData_API_Response(
                new TreasureData_API_Request(),
                new TreasureData_API_Stream_InputStream('{
  "database": "db0",
  "tables": [
    {
      "name": "access_log",
      "count": 13123233
    },
    {
      "name": "payment_log",
      "count": 331232
    }
  ]
}'
                ))));

        $api->setDriver($stub);
        $result = $api->getTableList("db0");

        $this->assertInstanceof('TreasureData_API_Result', $result);
        $message = $result->getResult();

        $this->assertInstanceof('TreasureData_API_Message_TableList', $message);
        /** @var TreasureData_API_Message_TableList $message */

        $this->assertEquals("db0", $message->getDatabase());

        $i = 0;
        $expected = array(
            array(
                "name" => "access_log",
                "count" => 13123233,
            ),
            array(
                "name" => "payment_log",
                "count" => 331232,
            ),
        );

        foreach ($message->getTables() as $table) {
            /** @var TreasureData_API_Message_Table $table */

            $this->assertInstanceof("TreasureData_API_Message_Table", $table);
            $this->assertEquals($expected[$i]['count'], $table->getCount());
            $this->assertEquals($expected[$i]['name'], $table->getName());
            $i++;
        }
    }

}