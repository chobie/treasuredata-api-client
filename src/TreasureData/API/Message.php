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
abstract class TreasureData_API_Message
{
    public function __construct($values = array())
    {
        $reflection_class = new ReflectionClass($this);
        foreach ($reflection_class->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            /* @var ReflectionProperty $property */
            if (array_key_exists($property->getName(), $values)) {
                $name = $property->getName();
                $comment = $property->getDocComment();

                if (preg_match("/array<(.+)?>/", $comment, $match)) {
                    $class_name = $match[1];
                    if ($class_name == "TreasureData_API_Message_TableSchema") {
                        $schema = json_decode($values[$name], true);
                        foreach ($schema as $value) {
                            array_push($this->$name, new $class_name(array("name" => $value[0], "type" => $value[1])));
                        }
                    } else {
                        foreach ($values[$name] as $value) {
                            array_push($this->$name, new $class_name($value));
                        }
                    }
                } else if (preg_match("/@var\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/", $comment, $match)) {
                    if (!in_array($match[1], array("string", "boolean", "integer"))) {
                        $class_name = $match[1];
                        if (class_exists($class_name)) {
                            $this->$name = new $class_name($values[$name]);
                        } else {
                            throw new InvalidArgumentException(sprintf("don't know %s type.", $match[1]));
                        }
                    } else {
                        switch ($match[1]) {
                        case "string":
                            $this->$name = (string)$values[$name];
                            break;
                        case "boolean":
                            $this->$name = (bool)$values[$name];
                            break;
                        case "integer":
                            $this->$name = (int)$values[$name];
                        break;
                        }

                    }
                } else {
                    $this->$name = $values[$name];
                }
            }
        }
    }

    public function __call($method, $arguments = array())
    {
        list($type, $property) = explode("_", self::underscore($method), 2);

        if ($type == "has") {
            return $this->has($property);
        } else if ($type == "get") {
            return $this->get($property);
        } else {
            throw new RuntimeException($method . " is not declared");
        }
    }

    /**
     * property getter
     *
     * @param $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->$name;
        } else {
            throw new InvalidArgumentException(sprintf("the property %s does not defined", $name));
        }
    }

    /**
     * check the property exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        if (property_exists($this, $name)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * returns representation of an array
     *
     * @api
     * @return array
     */
    public function toArray()
    {
        $result = array();
        $reflection_class = new ReflectionClass($this);

        foreach ($reflection_class->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            /** @var ReflectionProperty $property */
            $tmp = $this->get($property->getName());

            if (is_array($tmp)) {
                foreach ($tmp as $offset => $val) {
                    if (is_object($val)) {
                        if ($val instanceof TreasureData_API_Message) {
                            $result[$property->getName()][$offset] = $val->toArray();
                        } else if ($val instanceof DateTime) {
                            /* NB: php5.2 doesn't support getTimestamp method */
                            $result[$property->getName()][$offset] = $val->format("U");
                        } else {
                            $result[$property->getName()][$offset] = (string)$val;
                        }
                    } else {
                        $result[$property->getName()][$offset] = $val;
                    }
                }
            } else {
                if (is_object($tmp)) {
                    if ($tmp instanceof TreasureData_API_Message) {
                        $result[$property->getName()] = $tmp->toArray();
                    } else if ($tmp instanceof DateTime) {
                        /* NB: php5.2 doesn't support getTimestamp method */
                        $result[$property->getName()] = $tmp->format("U");
                    } else {
                        $result[$property->getName()] = (string)$tmp;
                    }
                } else {
                    $result[$property->getName()] = $tmp;
                }
            }
        }

        return $result;
    }

    /**
     * convert cameraized string to underscore string
     *
     * @param $str
     * @return string
     */
    public static function underscore($str)
    {
        return strtolower(preg_replace('/(?!^)[A-Z]/', '_$0', $str));
    }
}