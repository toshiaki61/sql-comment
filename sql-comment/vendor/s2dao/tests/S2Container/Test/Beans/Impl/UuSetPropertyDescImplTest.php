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

use S2Container\Beans\Impl\BeanDescImpl;

use S2Container\Beans\Impl\UuSetPropertyDescImpl;

class UuSetPropertyDescImplTest extends \PHPUnit_Framework_TestCase {

    function testAutoValueUUSet() {
        $r = new \ReflectionClass('\S2Container\Test\Beans\Impl\M_S2Container_UuSetPropertyDescImpl');
        $b = new BeanDescImpl($r);
        $m = $b->newInstance([]);
        $m->name = 'test-test';
        $this->assertEquals($m->getName(), "test-test");
    }

    function testAutoValueUUSet2() {
        $r = new \ReflectionClass('\S2Container\Test\Beans\Impl\M2_S2Container_UuSetPropertyDescImpl');
        $b = new BeanDescImpl($r);
        $m = $b->newInstance([]);
        $m->val = 'test-test';
        $this->assertEquals($m->getValue(), "test-test");
    }

    /**
     * @expectedException S2Container\Beans\Exception\PropertyNotFoundRuntimeException
     */
    function testAutoValueUUSet3() {
        $r = new \ReflectionClass('\S2Container\Test\Beans\Impl\M3_S2Container_UuSetPropertyDescImpl');
        $b = new BeanDescImpl($r);
        $b->getPropertyDesc('val');
    }
}

class M_S2Container_UuSetPropertyDescImpl {
    private $name;

    function __set($name, $value) {
        S2Logger::getLogger()->debug(__METHOD__ . ' called.', __METHOD__);
        $this->$name = $value;
    }

    function getName() {
        return $this->name;
    }
}

class M2_S2Container_UuSetPropertyDescImpl {
    private $val;

    function __set($name, $value) {
        S2Logger::getLogger()->debug(__METHOD__ . ' called.', __METHOD__);
        $this->$name = $value;
        S2Logger::getLogger()->debug("property : $name, value : $value ", __METHOD__);
    }

    function getValue() {
        return $this->val;
    }
}

class M3_S2Container_UuSetPropertyDescImpl {
}
