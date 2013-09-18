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
class TreasureData_API
{
    const DEFAULT_ENDPOINT    = 'https://api.treasure-data.com';

    const DEFAULT_API_VERSION = 'v3';

    const REQUEST_GET         = 'GET';
    const REQUEST_POST        = 'POST';

    const PRIORITY_VERY_LOW   = -2;
    const PRIORITY_NORMAL     =  0;
    const PRIORITY_HIGH       =  2;

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

    /**
     * @param string                          $endpoint
     * @param TreasureData_API_Driver         $driver
     * @param TreasureData_API_Authentication $authentication
     * @param TreasureData_API_Unpacker         $packer
     */
    public function __construct($endpoint = self::DEFAULT_ENDPOINT,
                                TreasureData_API_Driver $driver = null,
                                TreasureData_API_Authentication $authentication = null,
                                $api_version = self::DEFAULT_API_VERSION
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
    public function api($request_method = self::REQUEST_GET, $query, $params = array(), $gziped = false)
    {
        $builder    = new TreasureData_API_RequestBuilder();
        $builder->setApiVersion($this->getApiVersion());
        $builder->setRequestMethod($request_method);
        $builder->setEndPoint($this->endpoint);
        $builder->setQuery($query);
        $builder->setParams($params);
        $builder->setAuthentication($this->getAuthentication());

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
    public function get($endpoint, $params = array(), $gziped = false)
    {
        return $this->api(self::REQUEST_GET, $endpoint, $params, $gziped);
    }

    /**
     * @param       $endpoint
     * @param array $params
     * @return TreasureData_API_Result
     */
    public function post($endpoint, $params = array(), $gziped = false)
    {
        return $this->api(self::REQUEST_POST, $endpoint, $params, $gziped);
    }

    /**
     * returns a list of your databases.
     *
     * @api
     * @return TreasureData_API_Result
     * @see http://docs.treasure-data.com/articles/rest-api#get-v3databaselist
     */
    public function getDatabaseList()
    {
        $result = $this->get('/database/list');
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_DATABASES);

        return $result;
    }

    /**
     * @api
     * @param $database_name
     * @return TreasureData_API_Result
     * @see http://docs.treasure-data.com/articles/rest-api#get-v3tablelistdatabase
     */
    public function getTableList($database_name)
    {
        $result =  $this->get(sprintf('/table/list/%s', $database_name));
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_TABLE_LIST);

        return $result;
    }

    /**
     * Swaps the contents of two tables.
     *
     * @api
     * @param $database_name
     * @param $left_table_name
     * @param $right_table_name
     * @see http://docs.treasure-data.com/articles/rest-api#post-v3tableswapdatabasetable1table2
     */
    public function swapTable($database_name, $left_table_name, $right_table_name)
    {
        $result = $this->post(sprintf('/table/swap/%s/%s/%s', $database_name, $left_table_name, $right_table_name));
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_SWAP_TABLE);

        return $result;
    }

    /**
     * issues hive query
     *
     * @api
     * @param $database
     * @param $query
     * @param $priority
     * @see http://docs.treasure-data.com/articles/rest-api#post-v3jobissuehivedatabase
     */
    public function issueHiveQuery($database_name, $query, $priority = self::PRIORITY_NORMAL)
    {
        $result = $this->post(sprintf('/job/issue/hive/%s', $database_name), array(
            'query'    => $query,
            'priority' => $priority,
        ));
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_ISSUE_HIVE_JOB);

        return $result;
    }

    /**
     * shows the status of a specific job. It is faster and more robust than the /v3/job/show/:job_id command.
     *
     * @param $job_id
     * @return TreasureData_API_Result
     * @see http://docs.treasure-data.com/articles/rest-api#get-v3jobstatusjobid
     */
    public function getJobStatus($job_id)
    {
        $result = $this->get(sprintf('/job/status/%d', $job_id));
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_JOB_STATUS);

        return $result;
    }

    /**
     * shows the status and logs of a specific job.
     *
     * @param $job_id
     * @return TreasureData_API_Result
     * @see http://docs.treasure-data.com/articles/rest-api#get-v3jobshowjobid
     */
    public function showJob($job_id)
    {
        $result = $this->get(sprintf('/job/show/%d', $job_id));
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_JOB_INFO);

        return $result;
    }

    /**
     * kills the currently running job. The kill operation is performed asynchronously.
     *
     * @param $job_id
     * @see http://docs.treasure-data.com/articles/rest-api#post-v3jobkilljobid
     */
    public function killJob($job_id)
    {
        $result = $this->post(sprintf('/job/kill/%d', $job_id));
        $result->setMessageType(TreasureData_API_Result::MESSAGE_TYPE_KILL);

        return $result;
    }

    /**
     * returns the result of a specific job. Before issuing this command
     *
     * NB: please confirm that the job has been completed successfully via the /v3/job/show/:job_id command.
     *
     *
     * @param        $job_id
     * @param string $format
     * @return TreasureData_API_Result
     * @see http://docs.treasure-data.com/articles/rest-api#get-v3jobresultjobidformatmsgpackgz
     */
    public function getJobResult($job_id, $format = 'json')
    {
        $gziped = false;
        $available_formats = $this->getAvailableJobResultFormats();

        if (!in_array(strtolower($format), $available_formats)) {
            throw new InvalidArgumentException(
                sprintf("The format have to be in %s. passed %s", join(", ", $available_formats, $format))
            );
        }

        if ($format == self::FORMAT_MSGPACK_GZ) {
            $gziped = true;
        }

        $result = $this->get(sprintf('/job/result/%d', $job_id), array(
            'format' => $format,
        ), $gziped);

        $result->setUsePacker(true);
        $result->setPackerType($format);

        return $result;
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
