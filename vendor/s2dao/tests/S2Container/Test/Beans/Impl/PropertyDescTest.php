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
use S2Container\Beans\Impl\PropertyDescImpl;

use S2Container\Beans\Impl\BeanDescImpl;

class PropertyDescTest extends \PHPUnit_Framework_TestCase {

    function testToString() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\N_S2Container_PropertyDesc');
        $desc = new BeanDescImpl($a);
        $propDesc = $desc->getPropertyDesc('val1');
        $this->assertTrue($propDesc instanceof PropertyDescImpl);
        $this->assertEquals($propDesc->__toString(),
                "propertyName=val1,propertyType=null,readMethod=getVal1,writeMethod=null");
    }

    function testRWMethod() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\N_S2Container_PropertyDesc');
        $desc = new BeanDescImpl($a);
        $propDesc = $desc->getPropertyDesc('val1');
        $this->assertTrue($propDesc instanceof PropertyDescImpl);

        $propDesc = $desc->getPropertyDesc('val2');
        $m = $propDesc->getWriteMethod();
        $this->assertEquals($m->getName(), "setVal2");
        $m = $propDesc->getReadMethod();
        $this->assertEquals($m->getName(), "getVal2");
    }

    function testRWMethodOrdered() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\Impl\N_S2Container_PropertyDesc');
        $desc = new BeanDescImpl($a);

        $propDesc = $desc->getPropertyDesc('val3');
        $m = $propDesc->getWriteMethod();
        $this->assertEquals($m->getName(), "setVal3");
        $m = $propDesc->getReadMethod();
        $this->assertEquals($m->getName(), "getVal3");
    }
}

class N_S2Container_PropertyDesc {
    private $val1;
    private $val2;
    private $val3;

    function __construct($val1) {
        $this->val1 = $val1;
    }

    function getVal1() {
        return $this->val1;
    }

    function setVal2($val2) {
        $this->val2 = $val2;
    }

    function getVal2() {
        return $this->val2;
    }

    function getVal3() {
        return $this->val3;
    }

    function setVal3($val3) {
        $this->val3 = $val3;
    }
}
