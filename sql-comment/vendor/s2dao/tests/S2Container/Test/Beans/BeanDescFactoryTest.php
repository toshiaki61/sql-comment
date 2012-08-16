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
namespace S2Container\Test\Beans;
/**
 * @package org.seasar.framework.beans.factory
 * @author klove
 */
use S2Container\Beans\BeanDescFactory;

use S2Container\Beans\Impl\BeanDescImpl;

class BeanDescFactoryTest extends \PHPUnit_Framework_TestCase {

    function testGetBeanDesc() {
        $a = new \ReflectionClass('\S2Container\Test\Beans\A_S2Container_BeanDescFactory');
        $desc = BeanDescFactory::getBeanDesc($a);
        $this->assertTrue($desc instanceof BeanDescImpl);

        $a2 = new \ReflectionClass('\S2Container\Test\Beans\A_S2Container_BeanDescFactory');
        $this->assertEquals($a, $a2);
    }
}

class A_S2Container_BeanDescFactory {
}
