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
 * Class TreasureData_API_Message_Table
 *
 * @method string getName()
 * @method integer getCount()
 * @method string getType()
 * @method array getSchema()
 * @method integer getEstimatedStorageSize()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method DateTime getCounterUpdatedAt()
 * @method DateTime getLastLogTimestamp()
 */
class TreasureData_API_Message_Table extends TreasureData_API_Message
{
    /** @var  string $name */
    protected $name;

    /** @var  integer $count */
    protected $count;

    /** @var  string $type */
    protected $type;

    /** @var array<TreasureData_API_Message_TableSchema> $schema */
    protected $schema = array();

    /** @var  integer $estimated_storage_size */
    protected $estimated_storage_size;

    /** @var  DateTime $created_at */
    protected $created_at;

    /** @var  DateTime $updated_at */
    protected $updated_at;

    /** @var  DateTime $counter_updated_at */
    protected $counter_updated_at;

    /** @var  DateTime $last_log_timestamp */
    protected $last_log_timestamp;
}