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
class MethodUtilTest extends \PHPUnit_Framework_TestCase {

    function testGetSource() {
        $c = new \ReflectionClass('\S2Container\Test\Util\C_S2Container_MethodUtil');
        $src = \S2Container\Util\ClassUtil::getSource($c);
        $m = $c->getMethod('say');
        $src = \S2Container\Util\MethodUtil::getSource($m, $src);

        $this->assertEquals(trim($src[0]), "public function say() {");
        $this->assertEquals(trim($src[2]), "}");

        $src = \S2Container\Util\MethodUtil::getSource($m);
        $this->assertEquals(trim($src[0]), "public function say() {");
        $this->assertEquals(trim($src[2]), "}");

        $ref = new \ReflectionClass('\S2Container\Test\Util\IW_S2Container_MethodUtil');
        $src = \S2Container\Util\ClassUtil::getSource($ref);
        $m = $ref->getMethod('wm1');
        $src = \S2Container\Util\MethodUtil::getSource($m, $src);
        $this->assertEquals(trim($src[0]),
                'function wm1($arg1 = null, IA_S2Container_MethodUtil &$a);');
    }

    function testInvoke() {
        $t = new Test_S2Container_MethodUtil();
        $ref = new \ReflectionClass('\S2Container\Test\Util\Test_S2Container_MethodUtil');

        $m = $ref->getMethod('a');
        $ret = \S2Container\Util\MethodUtil::invoke($m, $t, array());
        $this->assertTrue($ret);

        $m = $ref->getMethod('a');
        $ret = \S2Container\Util\MethodUtil::invoke($m, $t, null);
        $this->assertTrue($ret);

        $m = $ref->getMethod('b');
        $ret = \S2Container\Util\MethodUtil::invoke($m, $t, array('hoge'));
        $this->assertEquals($ret, 'hoge');

        $m = $ref->getMethod('c');
        $ret = \S2Container\Util\MethodUtil::invoke($m, $t, array(1, 2));
        $this->assertEquals($ret, 3);

        $m = $ref->getMethod('d');
        $ret = \S2Container\Util\MethodUtil::invoke($m, $t, array(array(1, 2)));
        $this->assertEquals($ret, 3);

        $m = $ref->getMethod('e');
        $ret = \S2Container\Util\MethodUtil::invoke($m, $t, array(array(1, 2)));
        $this->assertEquals($ret, 3);

        $m = $ref->getMethod('f');
        $ret = \S2Container\Util\MethodUtil::invoke($m,
            $t,
            array(
                array(
                    new A_S2Container_MethodUtil(),
                    new B_S2Container_MethodUtil())));
        $this->assertTrue($ret instanceof B_S2Container_MethodUtil);
    }

    /**
     * @expectedException S2Container\Exception\IllegalArgumentException
     */
    function testIllegalRelfection() {
        $ret = \S2Container\Util\MethodUtil::invoke(null,
            new A_S2Container_MethodUtil());
    }

    /**
     * @expectedException S2Container\Exception\IllegalArgumentException
     */
    function testIllegalObject() {
        $t = new Test_S2Container_MethodUtil();
        $ref = new \ReflectionClass('\S2Container\Test\Util\Test_S2Container_MethodUtil');
        $m = $ref->getMethod('a');

        $ret = \S2Container\Util\MethodUtil::invoke($m, null);
    }
}

class Test_S2Container_MethodUtil {

    function a() {
        return true;
    }

    function b($arg1) {
        return $arg1;
    }

    function c($arg1, $arg2) {
        return $arg1 + $arg2;
    }

    function d($arg) {
        return $arg[0] + $arg[1];
    }

    function e(&$arg) {
        return $arg[0] + $arg[1];
    }

    function f(&$arg) {
        return $arg[1];
    }
}

interface IA_S2Container_MethodUtil {
}
class A_S2Container_MethodUtil implements IA_S2Container_MethodUtil {
    function __construct() {
    }
}

interface IB_S2Container_MethodUtil {
}
class B_S2Container_MethodUtil extends A_S2Container_MethodUtil implements
    IB_S2Container_MethodUtil {
    function __construct() {
        parent::__construct();
    }
}

class C_S2Container_MethodUtil {
    private $name;
    function __construct($name) {
        $this->name = $name;
    }

    public function say() {
        return $this->name;
    }
}

interface IO_S2Container_MethodUtil {
    function om1();
    function om2();
}
interface IW_S2Container_MethodUtil extends IO_S2Container_MethodUtil {
    function wm1($arg1 = null, IA_S2Container_MethodUtil &$a);
    function wm2();
}
