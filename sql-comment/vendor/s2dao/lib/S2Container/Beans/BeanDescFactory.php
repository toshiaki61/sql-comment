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
namespace S2Container\Beans;
/**
 * @package org.seasar.framework.beans.factory
 * @author klove
 */
final class BeanDescFactory {
    private static $beanDescCache_ = array();

    /**
     * Singleton
     */
    private function __construct() {
    }

    /**
     * @param \ReflectionClass
     */
    public static function getBeanDesc(ReflectionClass $clazz) {
        if (array_key_exists($clazz->getName(), self::$beanDescCache_)) {
            $beanDesc = self::$beanDescCache_[$clazz->getName()];
        } else {
            $beanDesc = new \S2Container\Beans\Impl\BeanDescImpl($clazz);
            self::$beanDescCache_[$clazz->getName()] = $beanDesc;
        }
        return $beanDesc;
    }
}
