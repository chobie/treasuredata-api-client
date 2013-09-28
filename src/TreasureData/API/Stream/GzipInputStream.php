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
class TreasureData_API_Stream_GzipInputStream
    extends TreasureData_API_Stream_InputStream
{
    /* Note: this is workaround. */

    public function __construct($datasource)
    {
        $this->datasource = $datasource;
    }

    public function getAll()
    {
        $buffer = "";
        $counter = 0;
        $sum     = 0;
        $total   = 0;

        $last = microtime(true);
        $bwlimit = $this->getBwlimit();
        while (!gzeof($this->datasource)) {
            $tmp = $this->read();
            $bytes = strlen($tmp);

            $buffer .= $tmp;
            $sum   += $bytes;
            $total += $bytes;
            $counter++;

            if ($bwlimit > 0 && $sum > $bwlimit) {
                $current = microtime(true);
                $wait    = $current - $last;
                if ($wait < 1) {
                    $wait = 1 - $wait;
                    usleep($wait * 1000000);
                }
                $sum = 0;
                $last = microtime(true);
            }
        }

        return $buffer;
    }

    public function read()
    {
        return gzread($this->datasource, 8192);
    }

    public function readLine()
    {
        return gzgets($this->datasource, 8192);
    }

}