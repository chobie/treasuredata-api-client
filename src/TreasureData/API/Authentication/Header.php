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
class TreasureData_API_Authentication_Header
    implements TreasureData_API_Authentication
{
    /** @var string $api_key */
    private $api_key;

    public function __construct($api_key)
    {
        $this->checkApiKey($api_key);
        $this->api_key = $api_key;
    }

    public function setApiKey($api_key)
    {
        $this->checkApiKey($api_key);
        $this->api_key = $api_key;
    }

    public function getApiKey()
    {
        if ($this->api_key instanceof TreasureData_API_ConfigResolver) {
            return TreasureData_API_ConfigLoader::load($this->api_key->resolve());
        } else {
            return $this->api_key;
        }
    }

    public function getAsString()
    {
        return "TD1 " . (string)$this->getApiKey();
    }

    protected function checkApiKey($api_key)
    {
        if (empty($api_key)) {
            throw new InvalidArgumentException("at least requires api key");
        }
        if ($api_key instanceof TreasureData_API_ConfigResolver) {
            return;
        }
        if (!is_string($api_key)) {
            throw new InvalidArgumentException("api key have to be a string");
        }

    }
}
