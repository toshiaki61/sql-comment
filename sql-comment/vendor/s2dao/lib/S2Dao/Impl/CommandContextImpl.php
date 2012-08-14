<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright 2005-2007 the Seasar Foundation and the Others.            |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// |                                                                      |
// |     http://www.apache.org/licenses/LICENSE-2.0                       |
// |                                                                      |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,                        |
// | either express or implied. See the License for the specific language |
// | governing permissions and limitations under the License.             |
// +----------------------------------------------------------------------+
// | Authors: nowel                                                       |
// +----------------------------------------------------------------------+
// $Id: $
//
namespace S2Dao\Impl;
/**
 * @author nowel
 * @package org.seasar.s2dao.impl
 */
class CommandContextImpl implements \S2Dao\CommandContext {

    private static $logger = null;
    /**
     * @var array
     */
    private $args;
    /**
     * @var array
     */
    private $argTypes;
    private $sqlBuf = '';
    /**
     * @var \SplFixedArray
     */
    private $bindVariables;
    /**
     * @var \SplFixedArray
     */
    private $bindVariableTypes;
    private $enabled = false;
    private $parent;

    public function __construct($parent = null) {
        if (self::$logger === null) {
            //self::$logger = S2Container_S2Logger::getLogger(get_class($this));
        }
        $this->args = [];
        $this->argTypes = [];
        $this->sqlBuf = '';
        $this->bindVariables = new \SplFixedArray(100);
        $this->bindVariableTypes = new \SplFixedArray(100);
        $this->parent = $parent;
        $this->enabled = false;
    }

    public function getArg($name) {
        $case = strtolower($name);
        if (isset($this->args[$case])) {
            return $this->args[$case];
        } else if ($this->parent !== null) {
            return $this->parent
                ->getArg($name);
        } else {
            if (count($this->args) === 1) {
                return array_pop($this->args);
            }
            //self::$logger->info('Argument(' . $name . ') not found');
            return null;
        }
    }

    public function getArgType($name) {
        $case = strtolower($name);
        if (isset($this->argTypes[$case])) {
            return $this->argTypes[$case];
        } else if ($this->parent !== null) {
            return $this->parent
                ->getArgType($name);
        } else {
            if (count($this->argTypes) === 1) {
                return array_pop($this->argTypes);
            }
            //self::$logger->info('Argument(' . $name . ') not found');
            return null;
        }
    }

    public function addArg($name, $arg, $argType) {
        $case = strtolower($name);
        $this->args[$case] = $arg;
        $this->argTypes[$case] = $argType;
    }

    public function getSql() {
        return (string) $this->sqlBuf;
    }

    public function getBindVariables() {
        return $this->bindVariables
            ->toArray();
    }

    public function getBindVariableTypes() {
        return $this->bindVariableTypes
            ->toArray();
    }

    public function addSql($sql, $bindVariable = null, $bindVariableType = null) {
        if (is_array($bindVariable) && is_array($bindVariableType)) {
            $this->sqlBuf .= $sql;
            $c = count($bindVariable);
            for ($i = 0; $i < $c; ++$i) {
                $this->bindVariables
                    ->offsetSet($i, $bindVariable[$i]);
                $this->bindVariableTypes
                    ->offsetSet($i, $bindVariableType[$i]);
            }
            return $this;
        } else if ($bindVariable === null && $bindVariableType === null) {
            $this->sqlBuf .= $sql;
            return $this;
        } else {
            $this->sqlBuf .= $sql;
            $this->bindVariables
                ->offsetSet(0, $bindVariable);
            $this->bindVariableTypes
                ->offsetSet(0, $bindVariableType);
            return $this;
        }
    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    private function isNull($valueType = null) {
        return $valueType === null || $valueType == gettype(null);
    }
}
