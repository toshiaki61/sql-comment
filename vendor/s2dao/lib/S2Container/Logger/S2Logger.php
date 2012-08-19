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
// | Authors: klove                                                       |
// +----------------------------------------------------------------------+
//
// $Id: $
namespace S2Container\Logger;
/**
 * @package org.seasar.framework.log
 * @author klove
 */
final class S2Logger
{
    public static $LOGGER_FACTORY = '\S2Container\Logger\SimpleLoggerFactory';
    private static $loggerFactory = null;

    public static function setLoggerFactory($factory){
        self::$loggerFactory = $factory;
    }

    private function __construct() {}

    /**
     * @param string class name
     */
    public static final function getLogger($class = '')
    {
        return self::getLoggerFactory()->getInstance($class);
    }

    public static final function getLoggerFactory()
    {
        if (self::$loggerFactory === null) {
            self::$loggerFactory = new self::$LOGGER_FACTORY;
        }
        return self::$loggerFactory;
    }
}
