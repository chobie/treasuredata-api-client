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
    /**
     * GzipInput stream provides inflating gzip stream feature (RFC 1952).
     *
     * We don't have `real` stream implementation yet. for now, use gzinflate.
     *
     * @param $datasource string or stream resource
     */
    public function __construct($datasource)
    {
        $buffer = "";
        if (is_resource($datasource)) {
            while (!feof($datasource)) {
                $buffer .= fread($datasource, 8192);
            }
        } else {
            $buffer = $datasource;
        }

        /** FIXME: gzdecode can't use under php 5.4. we use gzinflate atm */
        parent::__construct(gzinflate(substr($buffer, 10, -8)));
    }
}