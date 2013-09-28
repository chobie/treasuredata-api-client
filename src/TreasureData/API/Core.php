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
abstract class TreasureData_API_Core
{
    const DEFAULT_ENDPOINT    = 'https://api.treasure-data.com';

    const DEFAULT_API_VERSION = 'v3';

    const REQUEST_GET         = 'GET';
    const REQUEST_POST        = 'POST';

    const PRIORITY_VERY_LOW   = -2;
    const PRIORITY_LOW        = -1;
    const PRIORITY_NORMAL     =  0;
    const PRIORITY_HIGH       =  1;
    const PRIORITY_VERY_HIGH  =  2;

    const FORMAT_TSV          = 'tsv';
    const FORMAT_CSV          = 'csv';
    const FORMAT_JSON         = 'json';
    const FORMAT_MSGPACK_GZ   = 'msgpack.gz';

    /** @var  string $endpoint */
    protected $endpoint;

    /** @var  TreasureData_API_Driver $driver */
    protected $driver;

    /** @var  TreasureData_API_Authentication $authentication */
    protected $authentication;

    /** @var string api version */
    protected $api_version = self::DEFAULT_API_VERSION;

    /** @var  string $proxy_address */
    protected $proxy;

    /**
     * @param string                          $endpoint
     * @param TreasureData_API_Driver         $driver
     * @param TreasureData_API_Authentication $authentication
     * @param TreasureData_API_Unpacker         $packer
     * @param string                          $proxy
     */
    public function __construct($endpoint = self::DEFAULT_ENDPOINT,
                                TreasureData_API_Driver $driver = null,
                                TreasureData_API_Authentication $authentication = null,
                                $api_version = self::DEFAULT_API_VERSION,
                                $proxy = null
    )
    {
        $this->endpoint = $endpoint;

        if (is_null($driver)) {

        }

        $this->driver = $driver;

        if (is_null($authentication)) {
            $authentication = new TreasureData_API_Authentication_Nothing();
        }

        $this->authentication = $authentication;

        if (is_null($api_version)) {
            $api_version = self::DEFAULT_API_VERSION;
        }

        $this->api_version = $api_version;
        $this->proxy = $proxy;
    }

    /**
     * set driver
     *
     * @param TreasureData_API_Driver $driver
     */
    public function setDriver(TreasureData_API_Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * get current driver
     *
     * @return TreasureData_API_Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }


    /**
     * set authenticator
     *
     * @param TreasureData_API_Authentication $authentication
     */
    public function setAuthentication(TreasureData_API_Authentication $authentication)
    {
        $this->authentication = $authentication;
    }


    /**
     * get current authenticator
     *
     * @return TreasureData_API_Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * get current api version
     *
     * @return string
     */
    public function getApiVersion()
    {
        return $this->api_version;
    }

    /**
     * @param string $request_method
     * @param        $query
     * @param        $params
     * @return TreasureData_API_Result
     */
    protected function api($request_method = self::REQUEST_GET, $query, $params = array(), $gziped = false)
    {
        $builder    = new TreasureData_API_RequestBuilder();
        $builder->setApiVersion($this->getApiVersion());
        $builder->setRequestMethod($request_method);
        $builder->setEndPoint($this->endpoint);
        $builder->setQuery($query);
        $builder->setParams($params);
        $builder->setAuthentication($this->getAuthentication());
        $builder->setProxy($this->proxy);

        if ($gziped) {
            $builder->setGzipHint(true);
        }
        if ($this->getDriver()->getUserAgent()) {
            $builder->setUserAgent($this->getDriver()->getUserAgent());
        }

        $request = $builder->build();
        $result  = new TreasureData_API_Result($this->driver->request($request));
        return $result;
    }

    /**
     * @param       $endpoint
     * @param array $params
     * @return TreasureData_API_Result
     */
    protected function get($endpoint, $params = array(), $gziped = false)
    {
        return $this->api(self::REQUEST_GET, $endpoint, $params, $gziped);
    }

    /**
     * @param       $endpoint
     * @param array $params
     * @return TreasureData_API_Result
     */
    protected function post($endpoint, $params = array(), $gziped = false)
    {
        return $this->api(self::REQUEST_POST, $endpoint, $params, $gziped);
    }

    /**
     * returns available formats for job result
     *
     * @return array
     */
    protected function getAvailableJobResultFormats()
    {
        return array(
            self::FORMAT_TSV,
            self::FORMAT_CSV,
            self::FORMAT_JSON,
            self::FORMAT_MSGPACK_GZ,
        );
    }
}
