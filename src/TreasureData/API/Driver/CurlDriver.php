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
class TreasureData_API_Driver_CurlDriver
    implements TreasureData_API_Driver
{
    protected $user_agent;

    public function __construct($context_config = array())
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    }

    public function getUserAgent()
    {
        return $this->user_agent;
    }

    public function setupOption($option = array())
    {
        if (count($option)) {
            curl_setopt_array($this->curl, $option);
        }
    }

    public function __destruct()
    {
    }

    public function request(TreasureData_API_Request $request)
    {

        $curl = curl_copy_handle($this->curl);

        curl_setopt($curl, CURLOPT_URL, $request->getUrl());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PORT, $request->getPort());

        $headers = array();
        foreach ($request->getHeaders() as $key => $value) {
            $headers[] = sprintf("%s: %s", $key, $value);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($request->isPost()) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request->getParams()));
        }

        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($http_code[0] == "4") {
            throw new TreasureData_API_Exception_HTTPException(sprintf("API Server returns %s code: %s", $http_code, $result));
        }
        $headers = array();

        $response = new TreasureData_API_Response($request, new TreasureData_API_Stream_InputStream($this->socket), $headers);
        return $response;
    }
}