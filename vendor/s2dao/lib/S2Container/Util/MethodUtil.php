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
final class MethodUtil {
    /**
     *
     */
    private function __construct() {
    }

    /**
     * @param \ReflectionMethod
     * @param object
     * @param array args
     */
    public static function invoke($method, $target, $args = null) {
        if (!$method instanceof \ReflectionMethod) {
            throw new \S2Container\Exception\IllegalArgumentException('args[0] must be <ReflectionMethod>');
        }
        if (!is_object($target)) {
            throw new \S2Container\Exception\IllegalArgumentException('args[1] must be <object>');
        }

        /*
        ### S2AOP enhanced class issue.
                if (count($args) == 0) {
                    return $method->invoke($target,array());
                }
         */
        $strArg = array();
        $o = count($args);
        for ($i = 0; $i < $o; $i++) {
            $strArg[] = "\$args[" . $i . "]";
        }
        $methodName = $method->getName();
        $cmd = 'return $target->' . $methodName . '(' . implode(',', $strArg) . ");";

        if (defined('S2CONTAINER_PHP5_DEBUG_EVAL') && S2CONTAINER_PHP5_DEBUG_EVAL) {
            \S2Container\Logger\S2Logger::getLogger(__CLASS__)->debug("[ $cmd ]",
                    __METHOD__);
        }
        return eval($cmd);
    }

    /**
     * @param ReflectioinMethod
     * @return boolean
     */
    public static function isAbstract(\ReflectionMethod $method) {
        return $method->isAbstract();
    }

    /**
     * @param \ReflectionMethod method
     * @param array result of S2Container_ClassUtil::getSource()
     */
    public static function getSource(\ReflectionMethod $method, $src = null) {
        if ($src == null) {
            $src = \S2Container\Util\ClassUtil::getSource($method->getDeclaringClass());
        }

        $def = array();
        $start = $method->getStartLine();
        $end = $method->getEndLine();

        for ($i = $start - 1; $i < $end; $i++) {
            $def[] = trim($src[$i]);
        }

        return $def;
    }
}
