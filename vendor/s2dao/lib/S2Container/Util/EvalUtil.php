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
final class EvalUtil {
    private function __construct() {
    }

    /**
     * @param string eval expression
     * @return string eval expression
     */
    public static function getExpression($expression) {
        $exp = $expression;
        if (!preg_match("/\sreturn\s/", $exp) && !preg_match("/\nreturn\s/",
            $exp) && !preg_match("/^return\s/", $exp)) {
            $exp = "return " . $exp;
        }

        return self::addSemiColon($exp);
    }

    /**
     * @param string eval expression
     * @return string eval expression
     */
    public static function addSemiColon($expression) {
        $exp = trim($expression);

        if (!preg_match("/;$/", $exp)) {
            $exp = $exp . ";";
        }

        return $exp;
    }
}

