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
        $context = stream_context_create(array(
            'http' => array(
                'method'          => $request->getRequestMethod(),
                'header'          => $request->getHeadersAsString(),
                'proxy'           => $request->getProxy(),
                'content'         => $request->getContentBody(),
                'request_fulluri' => $request->hasProxy(),
                'ignore_errors'   => true,
            ),
        ));

        /* Note: stream_socket_client does not support http(s) protocol. we have to use fopen here. */
        $socket  = fopen($request->getUrl(), 'r', false, $context);
        if (!is_resource($socket)) {
            throw new Exception("can't create stream socket.");
        }

        $headers      = array();
        $this->socket = $socket;
        $meta_data    = stream_get_meta_data($socket);
        if (isset($meta_data['wrapper_data'])) {
            foreach ($meta_data['wrapper_data'] as $value) {
                if (strpos($value, "HTTP/") === 0) {
                    list($dummy, $status, $dummy) = explode(" ", $value, 3);
                    $headers['HTTP_STATUS'] = $status;
                } else {
                    list($key, $value) = explode(":", $value, 2);
                    $headers[$key] = $value;
                }
            }
        }

        if ($headers['HTTP_STATUS'][0] != 2) {
            throw new TreasureData_API_Exception_HTTPException(sprintf("API Server returns %s code: %s", $headers['HTTP_STATUS'], fread($this->socket, 8192)));
        }

        $stream   = new TreasureData_API_Stream_InputStream($this->socket);
        $response = new TreasureData_API_Response($request, $stream, $headers);

        return $response;
    }

    protected function close($socket)
    {
        fclose($socket);
    }
}