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
class TreasureData_API_RequestBuilder
{
    protected $request_method = 'GET';

    protected $endpoint;

    protected $query;

    protected $gzip_hint = false;

    protected $params = array();

    protected $version = '1.0';

    protected $api_version = TreasureData_API::DEFAULT_API_VERSION;

    protected $authentication;

    protected $user_agent;

    protected $proxy;

    public function __construct()
    {
    }

    public function getUserAgent()
    {
        return $this->user_agent;
    }

    public function setUserAgent($user_agent)
    {
        $this->user_agend = $user_agent;
    }

    public function setAuthentication(TreasureData_API_Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    public function getAuthentication()
    {
        return $this->authentication;
    }

    public function setApiVersion($api_version)
    {
        $this->api_version = trim($api_version, '/');
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setRequestMethod($method)
    {
        $this->request_method = $method;
    }

    public function setEndPoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function getEndPoint()
    {
        return $this->endpoint;
    }

    public function setParams($params = array())
    {
        $this->params = $params;
    }


    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return TreasureData_API_Request
     */
    public function build()
    {
        $result = array(
            "scheme" => "https",
            "port" => 443,
        );
        $result['request_method'] = $this->getRequestMethod();

        $info    = parse_url($this->getEndPoint());

        if (isset($info['scheme'])) {
            $result['scheme'] = $info['scheme'];
        }

        if (isset($info['host'])) {
            $result['host'] = $info['host'];

            //$address = gethostbyname($info['host']);
            //$request->addAddress($address);
        } else {
            throw new Exception();
        }

        if (isset($info['port'])) {
            $result['port'] = $info['port'];
        } else {
            if ($result['scheme'] == 'http') {
                $result['port'] = 80;
            }
        }

        $result['params'] = $this->getParams();
        if ($this->getAuthentication()) {
            $result['headers']['Authorization'] = $this->getAuthentication()->getAsString();
        }

        if ($this->getUserAgent()) {
            //$request->addHeader("User-Agent", $this->getUserAgent());
        }

        if ($this->isPost()) {
            $data = http_build_query($this->getParams());

            $query = '/' . $this->getApiVersion() . '/' . ltrim($this->getQuery(), "/");
            $result['query_string'] = $query;
            $result['headers']['Content-Type'] = "application/x-www-form-urlencoded";
            $result['headers']['Content-Length'] = strlen($data);
            $result['content_body'] = $data;
        } else {
            if ($this->hasParams()) {
                $query = '/' . $this->getApiVersion() . '/' . ltrim($this->getQuery(), "/") . '?' . http_build_query($this->getParams());
            } else {
                $query = '/' . $this->getApiVersion() . '/' . ltrim($this->getQuery(), "/");
            }

            $result['query_string'] = $query;
        }

        $result['url'] = sprintf("%s://%s%s", $result['scheme'], $result['host'], $query);
        $result['gzip_hint'] = $this->getGzipHint();
        $result['proxy'] = $this->getProxy();

        $request = new TreasureData_API_Request($result);
        return $request;
    }

    public function getApiVersion()
    {
        return $this->api_version;
    }

    public function hasParams()
    {
        return (bool)count($this->params);
    }


    public function getParams()
    {
        return $this->params;
    }

    public function isPost()
    {
        if ($this->getRequestMethod() == 'POST') {
            return true;
        } else {
            return false;
        }
    }

    public function getRequestMethod()
    {
        return $this->request_method;
    }

    public function getGzipHint()
    {
        return $this->gzip_hint;
    }

    public function setGzipHint($gzip_hint)
    {
        $this->gzip_hint = $gzip_hint;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getProxy()
    {
        return $this->proxy;
    }
}
