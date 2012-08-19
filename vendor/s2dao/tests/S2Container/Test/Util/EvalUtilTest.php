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
class EvalUtilTest extends \PHPUnit_Framework_TestCase {

    function testGetExpression() {
        $exp = "100";
        $ret = "return 100;";
        $result = \S2Container\Util\EvalUtil::getExpression($exp);
        $this->assertEquals($ret, $result);

        $exp = '"hoge"';
        $ret = 'return "hoge";';
        $result = \S2Container\Util\EvalUtil::getExpression($exp);
        $this->assertEquals($ret, $result);

        $exp = 'return "hoge"';
        $ret = 'return "hoge";';
        $result = \S2Container\Util\EvalUtil::getExpression($exp);
        $this->assertEquals($ret, $result);

        $exp = 'return 1000';
        $ret = 'return 1000;';
        $result = \S2Container\Util\EvalUtil::getExpression($exp);
        $this->assertEquals($ret, $result);

        $exp = '1000;';
        $ret = 'return 1000;';
        $result = \S2Container\Util\EvalUtil::getExpression($exp);
        $this->assertEquals($ret, $result);
    }

    function testAddSemiColon() {
        $exp = '1000';
        $ret = '1000;';
        $result = \S2Container\Util\EvalUtil::addSemiColon($exp);
        $this->assertEquals($ret, $result);

        $exp = '1000;';
        $ret = '1000;';
        $result = \S2Container\Util\EvalUtil::addSemiColon($exp);
        $this->assertEquals($ret, $result);
    }
}
