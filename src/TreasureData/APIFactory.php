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
class TreasureData_APIFactory
{
    /**
     * @param array $config
     * @return TreasureData_API
     */
    public static function createClient($config = array())
    {
        $default_config = array(
            "endpoint"       => TreasureData_API::DEFAULT_ENDPOINT,
            "authentication" => "TreasureData_API_Authentication_Header",
            "api_key"        => new TreasureData_API_ConfigResolver_HomeConfigResolver(),
            "api_version"    => TreasureData_API::DEFAULT_API_VERSION,
            "proxy"          => getenv("HTTP_PROXY"),
            "driver"         => "TreasureData_API_Driver_StreamSocketDriver",
            "driver_option" => array(
            )
        );

        $config = array_merge($default_config, $config);
        if ($config['driver'] == "TreasureData_API_Driver_CurlDriver") {
            if (!extension_loaded("curl")) {
                throw new RuntimeException("your php does not support curl. please rebuild php");
            }
        } else if ($config['driver'] == "TreasureData_API_Driver_StreamSocketDriver") {
            if (!in_array("ssl", stream_get_transports())) {
                throw new RuntimeException("stream socket must support ssl transport. please rebuild php");
            }
            if (ini_get("allow_url_fopen") == false) {
                throw new RuntimeException("stream socket requires `allow_url_fopen`. please check your php.ini");
            }
        }

        if (!empty($config['proxy'])) {
            $info = parse_url($config['proxy']);
            switch($info['scheme']) {
            case "http":
                $proto = 'tcp';
                break;
            case 'https':
                $proto = "ssl";
                break;
            default:
                $proto = 'tcp';
            }

            $config['proxy'] = sprintf("%s://%s:%d", $proto, $info['host'], $info['port']);
        }

        $authentication_class = $config['authentication'];
        $authentication = new $authentication_class($config['api_key']);

        $driver_class = $config['driver'];
        $driver = new $driver_class();
        $driver->setupOption($config['driver_option']);

        $api = new TreasureData_API($config['endpoint'], $driver, $authentication, $config['api_version'], $config['proxy']);
        return $api;
    }
}
