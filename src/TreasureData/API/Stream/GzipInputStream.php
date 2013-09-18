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
     * @param $datasource string or stream resource
     */
    public function __construct($datasource)
    {
        parent::__construct($datasource);

        /* Note: some environment (OSX) may crush zlib.inflate filter. */
        stream_filter_append($this->datasource, "zlib.inflate", STREAM_FILTER_READ);
    }
}