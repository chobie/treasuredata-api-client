<?php
/**
 *  TreasureData API Client
 *
 *  Copyright (C) 2013 Shuhei Tanuma
 *
 *     Licensed under the Apache License, Version 2.0 (the "License");
 *     you may not use this file except in compliance with the License.
 *     You may obtain a copy of the License at
 *
 *         http://www.apache.org/licenses/LICENSE-2.0
 *
 *     Unless required by applicable law or agreed to in writing, software
 *     distributed under the License is distributed on an "AS IS" BASIS,
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *     See the License for the specific language governing permissions and
 *     limitations under the License.
 */
class TreasureData_API_Unpacker_JsonUnpacker
    implements TreasureData_API_Unpacker
{
    protected $information = array();

    public function setColumnInformation($information)
    {
        $this->information = $information;
    }

    public function unpack(TreasureData_API_Stream_InputStream $stream)
    {
        $result = array();
        while ($buffer = $stream->readLine()) {
            if (!empty($this->information)) {
                $tmp = array();
                $args = json_decode($buffer, true);
                foreach ($args as $offset => $value) {
                    $tmp[$this->information[$offset]->get("name")] = $value;
                }
                $result[] = $tmp;
            } else {
                $result[] = json_decode($buffer, true);
            }
        }

        return $result;
    }

    public function unpack2(TreasureData_API_Stream_InputStream $stream, $callback)
    {
        while ($buffer = $stream->readLine()) {
            if (!empty($this->information)) {
                $result = array();
                $args = json_decode($buffer, true);
                foreach ($args as $offset => $value) {
                    $result[$this->information[$offset]->get("name")] = $value;
                }
            } else {
                $result = json_decode($buffer, true);
            }
            call_user_func_array($callback, array($result));
        }
    }

}
