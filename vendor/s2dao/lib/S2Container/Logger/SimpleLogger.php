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
class SimpleLogger {
    const DEBUG = 1;
    const INFO = 2;
    const WARN = 3;
    const ERROR = 4;
    const FATAL = 5;

    /**
     */
    function __construct() {
        if (!defined('S2CONTAINER_PHP5_LOG_LEVEL')) {
            define('S2CONTAINER_PHP5_LOG_LEVEL', 3);
        }
    }

    /**
     *
     */
    private function _log($level, $msg = "", $methodName = "") {
        switch ($level) {
            case self::DEBUG:
                $logLevel = "DEBUG";
                break;
            case self::INFO:
                $logLevel = "INFO";
                break;
            case self::WARN:
                $logLevel = "WARN";
                break;
            case self::ERROR:
                $logLevel = "ERROR";
                break;
            case self::FATAL:
                $logLevel = "FATAL";
                break;
        }
        if (S2CONTAINER_PHP5_LOG_LEVEL <= $level) {
            if (defined('S2CONTAINER_PHP5_SIMPLE_LOG_FILE')) {
                file_put_contents(S2CONTAINER_PHP5_SIMPLE_LOG_FILE,
                    sprintf("[%-5s] %s - %s\n", $logLevel, $methodName, $msg),
                    FILE_APPEND | LOCK_EX);
            } else {
                printf("[%-5s] %s - %s\n", $logLevel, $methodName, $msg);
            }
        }
    }

    /**
     * @param string log message
     * @param string method name
     */
    public function debug($msg = "", $methodName = "") {
        $this->_log(self::DEBUG, $msg, $methodName);
    }

    /**
     * @param string log message
     * @param string method name
     */
    public function info($msg = "", $methodName = "") {
        $this->_log(self::INFO, $msg, $methodName);
    }

    /**
     * @param string log message
     * @param string method name
     */
    public function warn($msg = "", $methodName = "") {
        $this->_log(self::WARN, $msg, $methodName);
    }

    /**
     * @param string log message
     * @param string method name
     */
    public function error($msg = "", $methodName = "") {
        $this->_log(self::ERROR, $msg, $methodName);
    }

    /**
     * @param string log message
     * @param string method name
     */
    public function fatal($msg = "", $methodName = "") {
        $this->_log(self::FATAL, $msg, $methodName);
    }
}
