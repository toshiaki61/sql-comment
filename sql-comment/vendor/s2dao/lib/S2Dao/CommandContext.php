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
namespace S2Dao;
/**
 * @author nowel
 * @package org.seasar.s2dao
 */
interface CommandContext {

    /**
     * @param string $name
     * @return mixed
     */
    public function getArg($name);

    /**
     * @param string $name
     * @return string
     */
    public function getArgType($name);

    /**
     * @param string $name
     * @param mixed $arg
     * @param string $argType
     */
    public function addArg($name, $arg, $argType);

    /**
     * @return string
     */
    public function getSql();

    /**
     * @return array
     */
    public function getBindVariables();

    /**
     * @return array
     */
    public function getBindVariableTypes();

    /**
     * @param string $sql
     * @param array $bindVariable
     * @param array $bindVariableType
     * @return S2Dao\CommandContext
     */
    public function addSql($sql, $bindVariable = null, $bindVariableType = null);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);
}

