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
interface SqlTokenizer {

    const SQL = 1;
    const COMMENT = 2;
    const ELSE_ = 3;
    const BIND_VARIABLE = 4;
    const EOF = 99;

    /**
     * @return string token
     */
    public function getToken();
    /**
     * @return string
     */
    public function getBefore();
    /**
     * @return string
     */
    public function getAfter();
    /**
     * @return int position
     */
    public function getPosition();
    /**
     * @return int token type
     */
    public function getTokenType();
    /**
     * @return int token type
     */
    public function getNextTokenType();
    /**
     * @return int token type
     */
    public function next();
    /**
     * @return string token
     */
    public function skipToken();
    /**
     * @return int position
     */
    public function skipWhitespace();
}

