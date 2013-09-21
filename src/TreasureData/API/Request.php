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
    /** @var  string $url */
    protected $url;

    /** @var  string $host */
    protected $host;

    /** @var int $port */
    protected $port = 443;

    /** @var string $scheme */
    protected $scheme = 'https';

    /** @var string $http_version */
    protected $http_version = '1.0';

    /** @var string $request_method */
    protected $request_method = "GET";

    /** @var array $headers */
    protected $headers = array();

    /** @var  string $content_body */
    protected $content_body;

    /** @var string $query_string */
    protected $query_string = '/';

    protected $gzip_hint = false;

    /** @var array $params */
    protected $params = array();

    /** @var  string $user_agent */
    protected $user_agent;

    public function __construct($values = array())
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * get full url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * get query parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function getGzipHint()
    {
        return $this->gzip_hint;
    }

    /**
     * get protocol scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * get headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * get http version
     *
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->http_version;
    }

    /**
     * get http request message
     *
     * @return string
     */
    public function getRequestAsString()
    {
        $buffer = array();
        $buffer[] = sprintf("%s %s HTTP/%s", $this->getRequestMethod(), $this->getQueryString(), $this->getHttpVersion());
        $buffer[] = sprintf("Host: %s", $this->getHost());
        foreach ($this->getHeaders() as $key => $value) {
            $buffer[] = sprintf("%s: %s", $key, $value);
        }
        if ($this->getHttpVersion() == "1.1") {
            $buffer[] = "Connection: close";
        }
        $buffer[] = null;

        $result = join("\r\n", $buffer);
        $result .= "\r\n";

        if ($this->isPost()) {
            $result .= $this->getContentBody();
        }

        return $result;
    }

    /**
     * get content body
     *
     * @return string
     */
    public function getContentBody()
    {
        return $this->content_body;
    }

    /**
     * check request method is post.
     *
     * @return bool
     */
    public function isPost()
    {
        if ($this->getRequestMethod() == 'POST') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get query string
     *
     * @return string
     */
    public function getQueryString()
    {
        return $this->query_string;
    }

    /**
     * get request method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->request_method;
    }

    /**
     * get host (FQDN)
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * get port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}

