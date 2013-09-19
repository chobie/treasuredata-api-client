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
class TreasureData_API_Request
{
    protected $host;

    protected $addresses = array();

    protected $port = 443;

    protected $scheme = 'https';

    protected $http_version = '1.0';

    protected $request_method = "GET";

    protected $headers = array();

    protected $content_body;

    protected $query_string = '/';

    protected $gzip_hint = false;

    protected $params = array();

    public function __construct($values = array())
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getGzipHint()
    {
        return $this->gzip_hint;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function setQueryString($query_string)
    {
        $this->query_string = $query_string;
    }

    public function getUserAgent()
    {
        return "";
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHttpVersion()
    {
        return $this->http_version;
    }

    public function getRequestAsString()
    {
        $buffer = array();
        $buffer[] = sprintf("%s %s HTTP/%s", $this->getRequestMethod(), $this->getQueryString(), $this->getHttpVersion());
        $buffer[] = sprintf("Host: %s", $this->getHost());
        foreach ($this->getHeaders() as $key => $value) {
            $buffer[] = sprintf("%s: %s", $key, $value);
        }
        $buffer[] = null;

        $result = join("\r\n", $buffer);
        $result .= "\r\n";

        if ($this->isPost()) {
            $result .= $this->getContentBody();
        }

        return $result;
    }

    public function getContentBody()
    {
        return $this->content_body;
    }

    public function isPost()
    {
        if ($this->getRequestMethod() == 'POST') {
            return true;
        } else {
            return false;
        }
    }

    public function getQueryString()
    {
        return $this->query_string;
    }

    public function setRequestMethod($request_method)
    {
        $this->request_method = $request_method;
    }

    public function getRequestMethod()
    {
        return $this->request_method;
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }
}

