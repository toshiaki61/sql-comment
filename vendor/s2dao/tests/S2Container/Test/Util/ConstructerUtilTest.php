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
namespace S2Container\Test\Util;
/**
 * @package org.seasar.framework.container.util
 * @author klove
 */
class ConstructerUtilTest extends \PHPUnit_Framework_TestCase {

    function testInstance() {
        $a = \S2Container\Util\ConstructorUtil::newInstance(new \ReflectionClass('\S2Container\Test\Util\A_S2Container_ConstructerUtil'),
            array());
        $this->assertTrue($a instanceof A_S2Container_ConstructerUtil);

        $a = \S2Container\Util\ConstructorUtil::newInstance(new \ReflectionClass('\S2Container\Test\Util\A_S2Container_ConstructerUtil'),
            null);
        $this->assertTrue($a instanceof A_S2Container_ConstructerUtil);
    }

    function testInstanceWithArgs() {
        $c = \S2Container\Util\ConstructorUtil::newInstance(new \ReflectionClass('\S2Container\Test\Util\C_S2Container_ConstructerUtil'),
            array('hoge'));
        $this->assertTrue($c instanceof C_S2Container_ConstructerUtil);
    }

    /**
     * @expectedException \S2Container\Exception\IllegalArgumentException
     */
    function testIllegalRelfection() {
        $c = \S2Container\Util\ConstructorUtil::newInstance('\S2Container\Test\Util\C_S2Container_ConstructerUtil',
            array('hoge'));
    }
}

interface IA_S2Container_ConstructerUtil {
}
class A_S2Container_ConstructerUtil implements IA_S2Container_ConstructerUtil {
}

class C_S2Container_ConstructerUtil {
    private $name;
    function __construct($name) {
        $this->name = $name;
    }

    public function say() {
        return $this->name;
    }
}
