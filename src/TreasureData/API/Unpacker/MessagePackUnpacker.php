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
class TreasureData_API_Unpacker_MessagePackUnpacker
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
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("callback have to be callable object or callable array");
        }

        return $this->unpackImpl($stream, $callback);
    }

    protected function unpackImpl(TreasureData_API_Stream_InputStream $stream, $callback = null)
    {
        $unpacker = new MessagePackUnpacker();

        $result = array();
        $offset = 0;
        $flag = true;
        $call = false;

        if (is_callable($callback)) {
            $call = true;
            $result = true;
        }

        while (true) {
            if ($flag) {
                $buffer = $stream->read();
                $flag = false;
            }

            if (empty($buffer)) {
                break;
            }

            if ($unpacker->execute($buffer, $offset)) {
                $data = $unpacker->data();
                if (!empty($this->information)) {
                    $tmp = array();
                    foreach ($data as $index => $value) {
                        $tmp[$this->information[$index]->get("name")] = $value;
                    }
                    $data = $tmp;
                }

                if ($call) {
                    call_user_func_array($callback, array($data));
                } else {
                    $result[] = $data;
                }

                $unpacker->reset();
                $buffer = substr($buffer, $offset);
                $offset = 0;

                if (empty($buffer)) {
                    $flag = true;
                    continue;
                }
            }
        }

        return $result;
    }
}
