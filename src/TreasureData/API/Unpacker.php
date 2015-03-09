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
interface TreasureData_API_Unpacker
{
    /**
     * @param TreasureData_API_Stream_InputStream $stream
     * @return mixed
     */
    public function unpack(TreasureData_API_Stream_InputStream $stream);

    /**
     * @param TreasureData_API_Stream_InputStream $stream
     * @param                                     $callback
     * @return void
     */
    public function unpack2(TreasureData_API_Stream_InputStream $stream, $callback);


    public function setColumnInformation($information);
}
