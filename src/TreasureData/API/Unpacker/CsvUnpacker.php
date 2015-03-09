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
class TreasureData_API_Unpacker_CsvUnpacker
    implements TreasureData_API_Unpacker
{
    protected $information = array();

    public function setColumnInformation($information)
    {
        $this->information = $information;
    }

    public function unpack(TreasureData_API_Stream_InputStream $stream)
    {
        return $this->unpackImpl($stream);
    }

    public function unpack2(TreasureData_API_Stream_InputStream $stream, $callback)
    {
        $this->unpackImpl($stream, $callback);
    }

    protected function unpackImpl(TreasureData_API_Stream_InputStream $stream, $callable = null)
    {
        $result = array();

        while ($line = $stream->readLine()) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $args = explode(",", $line);
            $tmp = array();

            if (!empty($this->information)) {
                foreach ($args as $offset => $value) {
                    $tmp[$this->information[$offset]->get("name")] = $value;
                }
            } else {
                foreach ($args as $arg) {
                    $tmp[] = trim($arg);
                }
            }

            if ($callable) {
                call_user_func_array($callable, array($tmp));
            } else {
                $result[] = $tmp;
            }
        }

        return $result;
    }
}
