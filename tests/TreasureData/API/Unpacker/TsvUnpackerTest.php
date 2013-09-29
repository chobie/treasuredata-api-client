<?php

class TreasureData_API_Unpacker_TsvUnpackerTest extends PHPUnit_Framework_TestCase
{
    public function testUnpack()
    {
        $unpacker = new TreasureData_API_Unpacker_TsvUnpacker();
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "unpacker",
                "data.tsv"
            )));
        $stream = new TreasureData_API_Stream_InputStream($fixture);
        $result = $unpacker->unpack($stream);

        $this->assertEquals(1, $result[0][0]);
        $this->assertEquals(2, $result[0][1]);
        $this->assertEquals(3, $result[0][2]);
        $this->assertEquals(4, $result[1][0]);
        $this->assertEquals(5, $result[1][1]);
        $this->assertEquals(6, $result[1][2]);
    }

    public function testUnpack2()
    {
        $unpacker = new TreasureData_API_Unpacker_TsvUnpacker();
        $fixture = file_get_contents(
            TD_API_FIXTURE_PATH . join(DIRECTORY_SEPARATOR, array(
                "unpacker",
                "data.tsv"
            )));
        $stream = new TreasureData_API_Stream_InputStream($fixture);
        $unpacker->unpack2($stream, array($this, "unpack2Assert"));
    }

    public function unpack2Assert($result)
    {
        static $state = 0;

        if ($state == 0) {
            $this->assertEquals(1, $result[0]);
            $this->assertEquals(2, $result[1]);
            $this->assertEquals(3, $result[2]);
            $state = 1;
        } else {
            $this->assertEquals(4, $result[0]);
            $this->assertEquals(5, $result[1]);
            $this->assertEquals(6, $result[2]);
        }
    }
}