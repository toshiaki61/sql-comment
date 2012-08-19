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
     * @var array
     */
    private $bindVariables;
    /**
     * @var array
     */
    private $bindVariableTypes;
    private $enabled = false;
    private $parent;

    /**
     * Constructs CommandContextImpl
     *
     * @param S2Dao\CommandContext $parent
     */
    public function __construct($parent = null) {
        if (self::$logger === null) {
            self::$logger = \S2Container\Logger\S2Logger::getLogger(__CLASS__);
        }
        $this->args = [];
        $this->argTypes = [];
        $this->sqlBuf = '';
        $this->bindVariables = [];
        $this->bindVariableTypes = [];
        $this->parent = $parent;
        $this->enabled = false;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::getArg()
     */
    public function getArg($name) {
        $case = strtolower($name);
        if (isset($this->args[$case])) {
            return $this->args[$case];
        } else if ($this->parent !== null) {
            return $this->parent
                ->getArg($name);
        }
        if (count($this->args) === 1) {
            return array_pop($this->args);
        }
        self::$logger->info('Argument(' . $name . ') not found', __METHOD__);
        return null;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::getArgType()
     */
    public function getArgType($name) {
        $case = strtolower($name);
        if (isset($this->argTypes[$case])) {
            return $this->argTypes[$case];
        } else if ($this->parent !== null) {
            return $this->parent
                ->getArgType($name);
        }
        if (count($this->argTypes) === 1) {
            return array_pop($this->argTypes);
        }
        self::$logger->info('Argument(' . $name . ') not found', __METHOD__);
        return null;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::addArg()
     */
    public function addArg($name, $arg, $argType) {
        $case = strtolower($name);
        $this->args[$case] = $arg;
        $this->argTypes[$case] = $argType;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::getSql()
     */
    public function getSql() {
        return (string) $this->sqlBuf;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::getBindVariables()
     */
    public function getBindVariables() {
        return $this->bindVariables;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::getBindVariableTypes()
     */
    public function getBindVariableTypes() {
        return $this->bindVariableTypes;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::addSql()
     */
    public function addSql($sql, $bindVariable = null, $bindVariableType = null) {
        if (is_array($bindVariable) && is_array($bindVariableType)) {
            $this->sqlBuf .= $sql;
            for ($i = 0, $c = count($bindVariable); $i < $c; ++$i) {
                $this->bindVariables[] = $bindVariable[$i];
                $this->bindVariableTypes[] = $bindVariableType[$i];
            }
        } else if ($bindVariable === null && $bindVariableType === null) {
            $this->sqlBuf .= $sql;
        } else {
            $this->sqlBuf .= $sql;
            $this->bindVariables[] = $bindVariable;
            $this->bindVariableTypes[] = $bindVariable;
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::isEnabled()
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.CommandContext::setEnabled()
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
}
