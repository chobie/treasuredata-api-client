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
class TreasureData_API_Driver_StreamSocketDriver
    implements TreasureData_API_Driver
{
    protected $socket;

    protected $context;

    protected $user_agent;

    public function __construct($context_config = array())
    {
        if ($context_config) {
            $this->context = stream_context_create($context_config);
        } else {
            $this->context = stream_context_create();
        }
    }

    public function getUserAgent()
    {
        return $this->user_agent;
    }

    public function setupOption($option = array())
    {
        if ($option) {
            $this->context = stream_context_create($option);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->socket)) {
            $this->close($this->socket);
        }
    }

    public function request(TreasureData_API_Request $request)
    {
        $socket  = $this->connect($request);
        $headers = array();

        if (!is_resource($socket)) {
            throw new Exception("can't create stream socket.");
        }

        $this->socket = $socket;
        $message = $request->getRequestAsString();


        $this->write($message);
        $flag = true;
        while ($flag) {
            $tmp = fgets($this->socket, 8192);
            if (trim($tmp) == "") {
                $flag = false;
                break;
            }

            if (strpos($tmp, "HTTP") === 0) {
                list($dummy, $status, $http_message) = explode(" ", $tmp, 3);
                $headers['HTTP_STATUS'] = $status;
            } else {
                /* Note: don't care about 2 lines at this time. */
                list($key, $value) = explode(":", $tmp, 2);

                $headers[$key] = $value;
            }
        }

        if ($headers['HTTP_STATUS'] == 404) {
            throw new TreasureData_API_Exception_HTTPException("API Server returns 404 not found");
        }

        if ($request->getGzipHint()) {
            $response = new TreasureData_API_Response($request, new TreasureData_API_Stream_GzipInputStream($this->socket), $headers);
        } else {
            $response = new TreasureData_API_Response($request, new TreasureData_API_Stream_InputStream($this->socket), $headers);
        }

        return $response;
    }

    protected function write($message)
    {
        fwrite($this->socket, $message);
    }

    protected function close($socket)
    {
        fclose($socket);
    }

    protected function connect(TreasureData_API_Request $request)
    {
        if (!is_null($this->socket)) {
            $this->close($this->socket);
        }

        $protocol = 'tcp';
        if ($request->getScheme() == 'https') {
            $protocol = 'ssl';
        }

        $proto = sprintf("%s://%s:%d", $protocol, $request->getHost(), $request->getPort());
        $socket = stream_socket_client($proto,
            $err,
            $errstr,
            60,
            STREAM_CLIENT_CONNECT,
            $this->context
        );

        return $socket;
    }
}