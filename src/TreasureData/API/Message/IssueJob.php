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
 * Class TreasureData_API_Message_IssueJob
 *
 * @method string getJobId()
 * @method string getType()
 * @method string getDatabase()
 * @method string getUrl()
 */
class TreasureData_API_Message_IssueJob extends TreasureData_API_Message
{
    /** @var  string $job_id */
    protected $job_id;

    /** @var  string $type */
    protected $type;

    /** @var  string $database */
    protected $database;

    /** @var  string $url */
    protected $url;
}