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
class U_S2Container_ClassUtil {
    function __construct(&$arg) {
    }
}

/**
 * @package org.seasar.framework.container.util
 * @author klove
 */
class ClassUtilTest extends \PHPUnit_Framework_TestCase {

    function testGetClassSource() {
        $uRef = new \ReflectionClass('\S2Container\Test\Util\U_S2Container_ClassUtil');
        $src = \S2Container\Util\ClassUtil::getClassSource($uRef);
        $this->assertEquals(trim($src[0]), "class U_S2Container_ClassUtil {");
        $this->assertEquals(trim($src[count($src) - 1]), "}");
    }

    function testGetSource() {
        $uRef = new \ReflectionClass('\S2Container\Test\Util\U_S2Container_ClassUtil');
        $src = \S2Container\Util\ClassUtil::getSource($uRef);
        $this->assertEquals(trim($src[24]), "class U_S2Container_ClassUtil {");
        $this->assertEquals(trim($src[26]), "}");
    }

    /**
     * @expectedException \S2Container\Exception\NoSuchMethodRuntimeException
     */
    function testGetMethod() {
        $cRef = new \ReflectionClass('\S2Container\Test\Util\C_S2Container_ClassUtil');
        $mRef = \S2Container\Util\ClassUtil::getMethod($cRef, 'say');
        $this->assertEquals($mRef->getName(), "say");

        $mRef = \S2Container\Util\ClassUtil::getMethod($cRef, 'say2');
        $this->assertTrue(false);
    }

    function testGetInterfaces() {
        $cRef = new \ReflectionClass('\S2Container\Test\Util\G_S2Container_ClassUtil');
        $this->assertEquals(count(\S2Container\Util\ClassUtil::getInterfaces($cRef)),
                1);

        $cRef = new \ReflectionClass('\S2Container\Test\Util\IW_S2Container_ClassUtil');
        $this->assertEquals(count(\S2Container\Util\ClassUtil::getInterfaces($cRef)),
                2);
    }

    function testGetNoneRepeatInterfaces() {
        $cRef = new \ReflectionClass('\S2Container\Test\Util\G_S2Container_ClassUtil');
        $this->assertEquals(count(\S2Container\Util\ClassUtil::getNoneRepeatInterfaces($cRef)),
                1);

        $cRef = new \ReflectionClass('\S2Container\Test\Util\IW_S2Container_ClassUtil');
        $this->assertEquals(count(\S2Container\Util\ClassUtil::getNoneRepeatInterfaces($cRef)),
                1);

        $cRef = new \ReflectionClass('\S2Container\Test\Util\B_S2Container_ClassUtil');
        $this->assertEquals(count(\S2Container\Util\ClassUtil::getNoneRepeatInterfaces($cRef)),
                1);
    }
}

class C_S2Container_ClassUtil {
    private $name;
    function __construct($name) {
        $this->name = $name;
    }

    public function say() {
        return $this->name;
    }
}

class G_S2Container_ClassUtil implements IG_S2Container_ClassUtil {
    function finish() {
        print "destroy class G \n";
    }

    function finish2($msg) {
        print "$msg G \n";
    }
}

interface IG_S2Container_ClassUtil {
}

interface IO_S2Container_ClassUtil {
    function om1();
    function om2();
}

interface IW_S2Container_ClassUtil extends IO_S2Container_ClassUtil {
    function wm1($arg1 = null, IA &$a);
    function wm2();
}

class A_S2Container_ClassUtil implements IO_S2Container_ClassUtil {
    function om1() {
    }
    function om2() {
    }
}
class B_S2Container_ClassUtil extends A_S2Container_ClassUtil implements
    IW_S2Container_ClassUtil {
    function wm1($arg1 = null, IA &$a) {
    }
    function wm2() {
    }
}
