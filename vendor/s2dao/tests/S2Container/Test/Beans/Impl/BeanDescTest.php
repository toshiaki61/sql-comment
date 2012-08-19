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
// $Id:$
namespace S2Container\Test\Beans\Impl;
/**
 * @package org.seasar.framework.beans.impl
 * @author klove
 */
use S2Container\Logger\S2Logger;

use S2Container\Beans\Impl\PropertyDescImpl;

use S2Container\Beans\Impl\BeanDescImpl;

class BeanDescTest extends \PHPUnit_Framework_TestCase {

    function testBeanDesc() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\Sample1_S2Container_BeanDesc');
        $desc = new BeanDescImpl($a);
        $this->assertTrue($desc instanceof BeanDescImpl);

        $b = $desc->getBeanClass();
        $this->assertEquals($a, $b);

        $constructor = $desc->getSuitableConstructor();
        $this->assertTrue($constructor instanceof \ReflectionMethod);
        $this->assertEquals($constructor->getName(), '__construct');
    }

    function testGetPropertyDesc() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\Sample1_S2Container_BeanDesc');
        $desc = new BeanDescImpl($a);

        $this->assertTrue($desc->hasPropertyDesc('val'));
        $this->assertTrue($desc->hasPropertyDesc('msg'));
        $this->assertTrue(!$desc->hasPropertyDesc('val3'));

        $propDesc = $desc->getPropertyDesc('val');
        $this->assertTrue($propDesc instanceof PropertyDescImpl);
        $this->assertEquals($propDesc->getPropertyName(), 'val');
        $this->assertTrue($propDesc->hasWriteMethod());
        $this->assertTrue($propDesc->hasReadMethod());
        $readMetnod = $propDesc->getReadMethod();
        $this->assertEquals($readMetnod->getName(), 'getVal');
        $writeMetnod = $propDesc->getWriteMethod();
        $this->assertEquals($writeMetnod->getName(), 'setVal');

        $propDesc = $desc->getPropertyDesc(0);
        $this->assertTrue($propDesc instanceof PropertyDescImpl);
        $this->assertEquals($propDesc->getPropertyName(), 'val');

        try {
            $propDesc = $desc->getPropertyDesc(2);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \S2Container\Beans\Exception\PropertyNotFoundRuntimeException);
        }

        try {
            $propDesc = $desc->getPropertyDesc('val2');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \S2Container\Beans\Exception\PropertyNotFoundRuntimeException);
        }
    }

    function testGetField() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\Sample1_S2Container_BeanDesc');
        $desc = new BeanDescImpl($a);

        $this->assertTrue($desc->hasField('QUERY_1'));
        $this->assertTrue($desc->hasField('QUERY_2'));
        $this->assertTrue(!$desc->hasField('QUERY_3'));

        $field = $desc->getField('QUERY_1');
        $this->assertTrue($field instanceof \ReflectionProperty);
        $this->assertEquals($field->getName(), 'QUERY_1');

        try {
            $this->assertEquals($field->getValue(), 'select * from talbe1;');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \ReflectionException);
        }

        try {
            $field = $desc->getField('QUERY_3');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \S2Container\Beans\Exception\FieldNotFoundRuntimeException);
            S2Logger::getLogger()->debug($e->getMessage(), __METHOD__);
        }
    }

    function testGetConstant() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\Sample1_S2Container_BeanDesc');
        $desc = new BeanDescImpl($a);

        $this->assertTrue($desc->hasConstant('BEAN_A'));
        $this->assertTrue($desc->hasConstant('BEAN_B'));
        $this->assertTrue(!$desc->hasConstant('BEAN_C'));

        $value = $desc->getConstant('BEAN_A');
        $this->assertEquals($value, 'TestBeanA');

        try {
            $field = $desc->getConstant('BEAN_C');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \S2Container\Beans\Exception\ConstantNotFoundRuntimeException);
            S2Logger::getLogger()->debug($e->getMessage(), __METHOD__);
        }
    }

    function testGetMethods() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\Sample1_S2Container_BeanDesc');
        $desc = new BeanDescImpl($a);

        $om = $desc->getMethods('om1');
        $this->assertTrue($om instanceof \ReflectionMethod);

        try {
            $om = $desc->getMethods('omX');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \S2Container\Beans\Exception\MethodNotFoundRuntimeException);
            S2Logger::getLogger()->debug($e->getMessage(), __METHOD__);
        }
    }
}

interface IO_S2Container_BeanDesc {
    function om1();
    function om2();
}

class Sample1_S2Container_BeanDesc implements IO_S2Container_BeanDesc {

    const BEAN_A = "TestBeanA";
    const BEAN_B = "TestBeanB";

    private static $QUERY_1 = "select * from talbe1;";
    public static $QUERY_2 = "select * from table2;";

    private $msg;
    private $val;

    function __construct() {
    }
    function om1() {
    }
    function om2() {
    }

    function setVal($val) {
        $this->val = $val;
    }
    function getVal() {
        return $this->val;
    }

    function setMsg($msg) {
        $this->msg = $msg;
    }
    function getMsg() {
        return $this->msg;
    }
}
