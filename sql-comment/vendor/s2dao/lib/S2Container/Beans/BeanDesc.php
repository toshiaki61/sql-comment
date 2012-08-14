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
 * @package org.seasar.framework.beans
 * @author klove
 */
interface BeanDesc {

    /**
     * @return \ReflectionClass
     */
    public function getBeanClass();

    /**
     * @param string property name
     * @return boolean
     */
    public function hasPropertyDesc($propertyName);

    /**
     * @param string property name
     */
    public function getPropertyDesc($propertyName);

    /**
     * @return int
     */
    public function getPropertyDescSize();

    /**
     * @param string field name
     * @return boolean
     */
    public function hasField($fieldName);

    /**
     * @param string field name
     * @return string
     */
    public function getField($fieldName);

    /**
     * @param array args
     */
    public function newInstance($args);

    /**
     *
     */
    public function getSuitableConstructor();

    /**
     * @param object target object
     * @param string method name
     * @param array args
     */
    public function invoke($target, $methodName, $args);

    /**
     * @param string method name
     */
    public function getMethods($methodName);

    /**
     * @param string method name
     * @return boolean
     */
    public function hasMethod($methodName);

    /**
     *
     */
    public function getMethodNames();

    /**
     * @param string constant name
     * @return boolean
     */
    public function hasConstant($constName);

    /**
     * @param string constant name
     */
    public function getConstant($constName);
}
