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
namespace S2Container\Util;
/**
 * @package org.seasar.framework.util
 * @author klove
 */
final class ConstructorUtil {
    /**
     *
     */
    private function __construct() {
    }

    /**
     * @param \ReflectionClass
     * @param array args
     */
    public static function newInstance($refClass, $args) {

        if (!$refClass instanceof \ReflectionClass) {
            throw new \S2Container\Exception\IllegalArgumentException('args[0] must be <ReflectionClass>');
        }

        $cmd = "return new " . $refClass->getName() . "(";
        $c = count($args);
        if ($c == 0) {
            $cmd = $cmd . ');';
        } else {
            $strArg = array();
            for ($i = 0; $i < $c; $i++) {
                $strArg[] = '$args[' . $i . ']';
            }
            $cmd = $cmd . implode(',', $strArg) . ');';
        }

        if (defined('S2CONTAINER_PHP5_DEBUG_EVAL') && S2CONTAINER_PHP5_DEBUG_EVAL) {
            \S2Container\Logger\S2Logger::getLogger(__CLASS__)->debug("[ $cmd ]",
                    __METHOD__);
        }
        return eval($cmd);
    }
}
