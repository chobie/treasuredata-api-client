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
class TreasureData_API_Response
{
    protected $request;
    protected $stream;
    protected $headers = array();

    public function __construct(TreasureData_API_Request $request, TreasureData_API_Stream_InputStream $stream, $headers = array())
    {
        $this->request = $request;
        $this->stream  = $stream;
        $this->headers = $headers;
    }

    /**
     * get http headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * return request object
     *
     * @return TreasureData_API_Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * get query string
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->request->getQueryString();
    }

    /**
     * @return TreasureData_API_Stream_InputStream
     */
    public function getInputStream()
    {
        return $this->stream;
    }
}
