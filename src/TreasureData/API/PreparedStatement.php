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
class TreasureData_API_PreparedStatement
{
    const TYPE_AUTO = 0x00;
    const TYPE_INT  = 0x01;

    protected $stmt;

    protected $replace = array();

    protected $keys = array();

    protected $placeholders = array();

    /**
     * @param $stmt $query
     */
    public function __construct($stmt)
    {
        $this->stmt = $stmt;

        preg_match_all("/:[a-zA-Z][a-zA-Z0-9_-]*/", $this->stmt, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $match) {
            if (!in_array($match[0], $this->keys)) {
                $this->keys[] = $match[0];
            }

            $this->placeholders[] = $match;
            $this->validated = false;
        }
    }

    /**
     * bind value
     *
     * @param     $key
     * @param     $value
     * @param int $opt
     * @throws InvalidArgumentException
     */
    public function bindValue($key, $value, $opt = self::TYPE_AUTO)
    {
        if (!in_array($key, $this->keys)) {
            throw new InvalidArgumentException(sprintf('placeholder %s does not exist. expects [%s]', $key, join(", ", $this->keys)));
        }

        $this->replace[$key] = array(
            "value" => $value,
            "opt"   => $opt,
        );
    }

    /**
     * @return mixed
     * @throws RuntimeException
     */
    public function __toString()
    {
        if (count($this->keys) != count($this->replace)) {
            throw new RuntimeException(sprintf("placeholder count does not match. expected %d, passed %d", count($this->keys), count($this->replace)));
        }

        $offset = 0;
        $buffer = $this->stmt;
        foreach ($this->placeholders as $placeholder) {
            $value = $this->replace[$placeholder[0]]["value"];
            if ($this->replace[$placeholder[0]]['opt'] == self::TYPE_AUTO) {
                if (is_string($value)) {
                    $value = sprintf("'%s'", $this->quote($value));
                }
            } else if ($this->replace[$placeholder[0]]['opt'] == self::TYPE_INT) {
                $value = (int)$value;
            }

            $buffer = substr_replace($buffer,
                $value,
                $offset + $placeholder[1],
                strlen($placeholder[0])
            );

            $offset += (strlen($this->replace[$placeholder[0]]["value"]) - strlen($placeholder[0]));
        }

        return $buffer;
    }

    /**
     * Currently, I expect single quote only. so please someone fix this operation.
     *
     * @param $value
     * @return string
     */
    protected function quote($value)
    {
        $new_value = "";
        $length = strlen($value);
        for ($i = 0; $i < $length; $i++) {
            if ($value[$i] == "'" || $value[$i] == "/") {
                $new_value .= "\\";
            }
            $new_value .= $value[$i];
        }

        return $new_value;
    }
}
