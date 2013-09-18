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

/**
 * Class TreasureData_API_Message_JobInformation
 *
 * @method string getJobId()
 * @method string getType()
 * @method string getUrl()
 * @method string getDatabase()
 * @method string getStatus()
 * @method string getStatusErr()
 * @method string getQuery()
 * @method integer getPriority()
 * @method integer getRetryLimit()
 * @method string getResult()
 * @method string getUserName()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method DateTime getStartAt()
 * @method DateTime getEndAt()
 * @method array getHiveResultSchema()
 * @method TreasureData_API_Message_JobInformationDebug getDebug()
 * @method string getOrganization()
 */
class TreasureData_API_Message_JobInformation extends TreasureData_API_Message
{
    /** @var  string $job_id */
    protected $job_id;

    /** @var  string $type */
    protected $type;

    /** @var  string $url */
    protected $url;

    /** @var  string $database */
    protected $database;

    /** @var  string $status */
    protected $status;

    /** @var  string $status_err */
    protected $status_err;

    /** @var  string $query */
    protected $query;

    /** @var  integer $priority */
    protected $priority;

    /** @var  integer $retry_limit */
    protected $retry_limit;

    /** @var  string $result */
    protected $result;

    /** @var  string $user_name */
    protected $user_name;

    /** @var  DateTime $created_at */
    protected $created_at;

    /** @var  DateTime $updated_at */
    protected $updated_at;

    /** @var  DateTime $start_at */
    protected $start_at;

    /** @var  DateTime $end_at */
    protected $end_at;

    /** @var  array<TreasureData_API_Message_TableSchema> $hive_result_schema */
    protected $hive_result_schema = array();

    /** @var  TreasureData_API_Message_JobInformationDebug $debug */
    protected $debug;

    /** @var  string $organization */
    protected $organization;
}