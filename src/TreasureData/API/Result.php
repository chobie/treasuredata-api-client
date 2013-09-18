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
    const MESSAGE_TYPE_ISSUE_HIVE_JOB = 4;
    const MESSAGE_TYPE_JOB_STATUS     = 5;
    const MESSAGE_TYPE_JOB_INFO       = 6;
    const MESSAGE_TYPE_KILL           = 7;

    const PACKER_TYPE_CSV = "csv";
    const PACKER_TYPE_TSV = "tsv";
    const PACKER_TYPE_JSON = "json";
    const PACKER_TYPE_MSGPACK_GZ = "msgpack.gz";

    protected $response;

    protected $message_type;

    protected $packer_type;

    protected $use_packer = false;

    protected $processed = false;

    protected $result;


    public function __construct(TreasureData_API_Response $response)
    {
        $this->response = $response;
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

    public function getResult()
    {
        if (!$this->processed) {
            $this->processed = true;

            if ($this->isUsePacker()) {
                $stream   = $this->getResponse()->getInputStream();
                $unpacker = $this->getPackerForType($this->getPackerType());
                $result = $unpacker->unpack($stream);

            } else {
                $message = $this->getResponse()->getInputStream()->getAll();
                $array   = json_decode($message, true);
                $result = $this->getMessageForType($this->getMessageType(), $array);
            }

            /* Note: retain result as most InputStream implementation can not rewind. */
            $this->result = $result;
        }

        return $this->result;
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
        case self::MESSAGE_TYPE_ISSUE_HIVE_JOB:
            return new TreasureData_API_Message_IssueHiveJob($array);
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
