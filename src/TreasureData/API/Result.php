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
class TreasureData_API_Result
{
    const MESSAGE_TYPE_DATABASES      = 1;
    const MESSAGE_TYPE_TABLE_LIST     = 2;
    const MESSAGE_TYPE_SWAP_TABLE     = 3;
    const MESSAGE_TYPE_ISSUE_JOB      = 4;
    const MESSAGE_TYPE_JOB_STATUS     = 5;
    const MESSAGE_TYPE_JOB_INFO       = 6;
    const MESSAGE_TYPE_KILL           = 7;

    const PACKER_TYPE_CSV = "csv";
    const PACKER_TYPE_TSV = "tsv";
    const PACKER_TYPE_JSON = "json";
    const PACKER_TYPE_MSGPACK_GZ = "msgpack.gz";

    /** @var TreasureData_API_Response $response */
    protected $response;

    /** @var  TreasureData_API $api */
    protected $api;

    protected $message_type;

    protected $packer_type;

    protected $use_packer = false;

    protected $processed = false;

    protected $use_dictionary = false;

    protected $result;

    protected $job_id;


    public function __construct(TreasureData_API_Response $response)
    {
        $this->response = $response;
    }

    public function setApi($api)
    {
        $this->api = $api;
    }

    public function setUseDictionary($flag)
    {
        $this->use_dictionary = $flag;
    }

    public function getUseDictionary()
    {
        return $this->use_dictionary;
    }

    public function setJobId($job_id)
    {
        $this->job_id = $job_id;
    }

    public function getJobId()
    {
        return $this->job_id;
    }

    public function setMessageType($type)
    {
        $this->message_type = $type;
    }

    public function getMessageType()
    {
        return $this->message_type;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setPackerType($type)
    {
        $this->packer_type = $type;
    }

    public function getPackerType()
    {
        return $this->packer_type;
    }

    public function setUsePacker($boolean)
    {
        $this->use_packer = $boolean;
    }

    public function isUsePacker()
    {
        return $this->use_packer;
    }

    public function shouldUseDictionary() {
        return true;
    }


    public function getResult($callback = null)
    {
        if (!$this->processed) {
            $this->processed = true;

            if ($this->isUsePacker()) {
                /* currently, this block only for get result api call. */

                $stream = $this->getStream($this->getResponse());
                $unpacker = $this->getPackerForType($this->getPackerType());

                if ($this->getUseDictionary()) {
                    $schema = $this->api->showJob($this->getJobId())->getResult()->get("hive_result_schema");
                    $unpacker->setColumnInformation($schema);
                }

                if (is_callable($callback)) {
                    $unpacker->unpack2($stream, $callback);
                    $result = true;
                } else {
                    $result = $unpacker->unpack($stream);
                }
            } else {
                /* most api results are very small. so we use getAll() here */
                $message = $this->getResponse()->getInputStream()->getAll();
                $array   = json_decode($message, true);
                $result = $this->getMessageForType($this->getMessageType(), $array);
            }

            /* Note: retain result as most InputStream implementation can not rewind. */
            $this->result = $result;
        }

        return $this->result;
    }

    protected function getStream(TreasureData_API_Response $response)
    {
        /* Unfortunately, PHP can't pass stream resource to gzopen.
            so we write result to disk and reopen with gzopen */
        if ($response->getRequest()->getGzipHint()) {
            $stream   = $this->getResponse()->getInputStream();
            $temp_file = tempnam(sys_get_temp_dir(), 'treasuredata-php-api-client');

            $fp = fopen($temp_file, "w");
            while ($buffer = $stream->read()) {
                fwrite($fp, $buffer);
            }
            fclose($fp);

            $stream = new TreasureData_API_Stream_GzipInputStream(gzopen($temp_file, "r"));
            register_shutdown_function("TreasureData_API_Result::removeTempFile", $temp_file);
        } else {
            $stream   = $this->getResponse()->getInputStream();
        }

        return $stream;
    }

    public static function removeTempFile($path)
    {
        /* Note: workaround for removing temp file. what a suck gzopen. */
        if (is_file($path)) {
            if (strpos($path, sys_get_temp_dir()) !== false) {
                unlink($path);
            }
        }
    }

    protected function getMessageForType($message_type, $array)
    {
        switch ($message_type) {
        case self::MESSAGE_TYPE_TABLE_LIST:
            return new TreasureData_API_Message_TableList($array);
            break;
        case self::MESSAGE_TYPE_DATABASES:
            return new TreasureData_API_Message_Databases($array);
            break;
        case self::MESSAGE_TYPE_SWAP_TABLE:
            return new TreasureData_API_Message_SwapTable($array);
            break;
        case self::MESSAGE_TYPE_ISSUE_JOB:
            return new TreasureData_API_Message_IssueJob($array);
            break;
        case self::MESSAGE_TYPE_JOB_STATUS:
            return new TreasureData_API_Message_JobStatus($array);
            break;
        case self::MESSAGE_TYPE_JOB_INFO:
            return new TreasureData_API_Message_JobInformation($array);
            break;
        case self::MESSAGE_TYPE_KILL:
            return new TreasureData_API_Message_Kill($array);
            break;
        default:
            /* Note: please add a case if you want to add not implemented feature. */
            return $array;
        }

    }

    protected function getPackerForType($packer_type)
    {
        switch ($packer_type) {
        case self::PACKER_TYPE_CSV:
            $unpacker = new TreasureData_API_Unpacker_CsvUnpacker();
            break;
        case self::PACKER_TYPE_TSV:
            $unpacker = new TreasureData_API_Unpacker_TsvUnpacker();
            break;
        case self::PACKER_TYPE_JSON:
            $unpacker = new TreasureData_API_Unpacker_JsonUnpacker();
            break;
        case self::PACKER_TYPE_MSGPACK_GZ:
            $unpacker = new TreasureData_API_Unpacker_MessagePackUnpacker();
            break;
        default:
            throw new InvalidArgumentException("not implemented yet");
        }

        return $unpacker;
    }
}

/* NB: ubuntu karmic(9.10) can't use gzopen as itself's bug. */
if (!function_exists("gzopen") && function_exists("gzopen64")) {
    function gzopen($file, $mode) {
        return gzopen64($file, $mode);
    }
}
