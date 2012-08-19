<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright 2005-2006 the Seasar Foundation and the Others.            |
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
// | Authors: nowel                                                       |
// +----------------------------------------------------------------------+
// $Id: $
//
namespace S2Dao\Test\Impl;
/**
 * @author nowel
 */
use S2Container\Logger\S2Logger;

use S2Dao\Exception\EndCommentNotFoundRuntimeException;

use S2Dao\Exception\TokenNotClosedRuntimeException;

use S2Dao\Impl\CommandContextImpl;

use S2Dao\Impl\SqlParserImpl;

class SqlParserImplTest extends \PHPUnit_Framework_TestCase {

    public function testParse() {
        $sql = "SELECT * FROM EMP2 emp2";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl;
        $node = $parser->parse();
        $node->accept($ctx);
        $this->assertEquals($sql, $ctx->getSql());
    }

    public function testParseEndSemicolon() {
        $this->parseEndSemicolon2(";");
        $this->parseEndSemicolon2(";\t");
        $this->parseEndSemicolon2("; ");
    }

    private function parseEndSemicolon2($endChar) {
        $sql = "SELECT * FROM EMP2 emp2";
        $parser = new SqlParserImpl($sql . $endChar);
        $ctx = new CommandContextImpl();
        $node = $parser->parse();
        $node->accept($ctx);
        $this->assertEquals($sql, $ctx->getSql());
    }

    /**
     * @expectedException S2Dao\Exception\TokenNotClosedRuntimeException
     */
    public function testCommentEndNotFound() {
        $sql = "SELECT * FROM EMP2 emp2/*hoge";
        $parser = new SqlParserImpl($sql);
        $parser->parse();
        $this->fail("期待通りの例外が発生しませんでした。");
    }

    public function testEndNodeStart() {
        $sql = "SELECT * FROM EMP2 /*END*/";
        $parser = new SqlParserImpl($sql);
        $node = $parser->parse();
        /* @var $node \S2Dao\Node */
        $this->assertEquals($node->getChildSize(), 1);
        $this->assertTrue($node->getChild(0) instanceof \S2Dao\Node\SqlNode);
    }

    public function testStartElseNodeWithoutIfNode() {
        $sql = "SELECT * FROM EMP2 --ELSE /*hoge*/";
        $parser = new SqlParserImpl($sql);
        $node = $parser->parse();
        /* @var $node \S2Dao\Node */
        $this->assertEquals($node->getChildSize(), 2);
        $this->assertTrue($node->getChild(0) instanceof \S2Dao\Node\SqlNode);
        $this->assertTrue($node->getChild(1) instanceof \S2Dao\Node\BindVariableNode);
    }

    /**
     * @expectedException S2Dao\Exception\IfConditionNotFoundRuntimeException
     */
    public function testEmptyIf() {
        $sql = "SELECT * FROM EMP2 /*IF*//*aaa*/''/*END*/";
        $parser = new SqlParserImpl($sql);
        $node = $parser->parse();
        /* @var $node \S2Dao\Node */
        $this->fail("期待通りの例外が発生しませんでした。");
    }

    public function testParseBindVariable4() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE job = #*job*#'CLERK' AND deptno = #*deptno*#20";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $job = "CLERK";
        $deptno = 20;
        $ctx->addArg("job", $job, gettype($job));
        $ctx->addArg("deptno", $deptno, gettype($deptno));
        $root = $parser->parse();
        $root->accept($ctx);
        $this->assertEquals($sql, $ctx->getSql());
    }

    public function testParseBindVariable() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE job = /*job*/'CLERK' AND deptno = /*deptno*/20";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE job = ? AND deptno = ?";
        $sql3 = "SELECT * FROM EMP2 emp2 WHERE job = ";
        $sql4 = " AND deptno = ";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $job = "CLERK";
        $deptno = 20;
        $ctx->addArg("job", $job, gettype($job));
        $ctx->addArg("deptno", $deptno, gettype($deptno));
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(2, count($vars));
        $this->assertEquals($job, $vars[0]);
        $this->assertEquals($deptno, $vars[1]);
        $this->assertEquals(4, $root->getChildSize());
        $sqlNode = $root->getChild(0);
        $this->assertEquals($sql3, $sqlNode->getSql());
        $varNode = $root->getChild(1);
        $this->assertEquals("job", $varNode->getExpression());
        $sqlNode2 = $root->getChild(2);
        $this->assertEquals($sql4, $sqlNode2->getSql());
        $varNode2 = $root->getChild(3);
        $this->assertEquals("deptno", $varNode2->getExpression());
    }

    public function testParseBindVariable2() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE job = /* job*/'CLERK'";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE job = 'CLERK'";
        $sql2_parsed = "SELECT * FROM EMP2 emp2 WHERE job = ?";
        $sql3 = "SELECT * FROM EMP2 emp2 WHERE job = ";
        $sql4 = " job";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2_parsed, $ctx->getSql());
        $this->assertEquals(2, $root->getChildSize());
        $sqlNode = $root->getChild(0);
        $this->assertEquals($sql3, $sqlNode->getSql());
        $sqlNode2 = $root->getChild(1);
        $this->assertEquals($sql4, $sqlNode2->getExpression());
    }

    public function testParseWhiteSpace() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE emp2no = /*emp2no*/1 AND 1 = 1";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE emp2no = ? AND 1 = 1";
        $sql3 = " AND 1 = 1";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $emp2no = 7788;
        $ctx->addArg("emp2no", $emp2no, gettype($emp2no));
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $sqlNode = $root->getChild(2);
        $this->assertEquals($sql3, $sqlNode->getSql());
    }

    public function testParseIf() {
        $sql = "SELECT * FROM EMP2 emp2/*IF job != null*/ WHERE job = /*job*/'CLERK'/*END*/";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE job = ?";
        $sql3 = "SELECT * FROM EMP2 emp2";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $job = "CLERK";
        $ctx->addArg("job", $job, gettype($job));
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars));
        $this->assertEquals($job, $vars[0]);
        $this->assertEquals(2, $root->getChildSize());
        $sqlNode = $root->getChild(0);
        $this->assertEquals($sql3, $sqlNode->getSql());
        $ifNode = $root->getChild(1);
        $this->assertEquals("job != null", $ifNode->getExpression());
        $this->assertEquals(2, $ifNode->getChildSize());
        $sqlNode2 = $ifNode->getChild(0);
        $this->assertEquals(" WHERE job = ", $sqlNode2->getSql());
        $varNode = $ifNode->getChild(1);
        $this->assertEquals("job", $varNode->getExpression());
        $ctx2 = new CommandContextImpl();
        $root->accept($ctx2);
        S2Logger::getLogger()->debug($ctx2->getSql(), __METHOD__);
        $this->assertEquals($sql3, $ctx2->getSql());
    }

    public function testParseIf2() {
        $sql = "/*IF aaa != null*/aaa/*IF bbb != null*/bbb/*END*//*END*/";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals("", $ctx->getSql());
        $ctx->addArg("aaa", null, gettype(""));
        $ctx->addArg("bbb", "hoge", gettype(""));
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals("", $ctx->getSql());
        $ctx->addArg("aaa", "hoge", gettype(""));
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals("aaabbb", $ctx->getSql());
        $ctx2 = new CommandContextImpl();
        $ctx2->addArg("aaa", "hoge", gettype(""));
        $ctx2->addArg("bbb", null, gettype(""));
        $root->accept($ctx2);
        S2Logger::getLogger()->debug("[" . $ctx2->getSql() . "]", __METHOD__);
        $this->assertEquals("aaa", $ctx2->getSql());
    }

    public function testParseElse() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE /*IF job != null*/job = /*job*/'CLERK'--ELSE job is null/*END*/";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE job = ?";
        $sql3 = "SELECT * FROM EMP2 emp2 WHERE job is null";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $job = "CLERK";
        $ctx->addArg("job", $job, gettype($job));
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars));
        $this->assertEquals($job, $vars[0]);
        $ctx2 = new CommandContextImpl();
        $root->accept($ctx2);
        S2Logger::getLogger()->debug("[" . $ctx2->getSql() . "]", __METHOD__);
        $this->assertEquals($sql3, $ctx2->getSql());
    }

    public function testParseElse2() {
        $sql = "/*IF false*/aaa--ELSE bbb = /*bbb*/123/*END*/";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $bbb = 123;
        $ctx->addArg("bbb", $bbb, gettype($bbb));
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals("bbb = ?", $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars));
        $this->assertEquals($bbb, $vars[0]);
    }

    public function testParseElse3() {
        $sql = "/*IF false*/aaa--ELSE bbb/*IF false*/ccc--ELSE ddd/*END*//*END*/";
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl();
        $root = $parser->parse();
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals("bbbddd", $ctx->getSql());
    }

    public function testElse4() {
        $sql = "SELECT * FROM EMP2 emp2/*BEGIN*/ WHERE /*IF false*/aaa--ELSE AND deptno = 10/*END*//*END*/";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno = 10";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
    }

    public function testBegin() {
        $sql = "SELECT * FROM EMP2 emp2/*BEGIN*/ WHERE /*IF job !== null*/job = /*job*/'CLERK'/*END*//*IF deptno !== null*/ AND deptno = /*deptno*/20/*END*//*END*/";
        $sql2 = "SELECT * FROM EMP2 emp2";
        $sql3 = "SELECT * FROM EMP2 emp2 WHERE job = ?";
        $sql4 = "SELECT * FROM EMP2 emp2 WHERE job = ? AND deptno = ?";
        $sql5 = "SELECT * FROM EMP2 emp2 WHERE deptno = ?";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());

        $ctx2 = new CommandContextImpl();
        $ctx2->addArg("job", "CLERK", gettype(""));
        $ctx2->addArg("deptno", null, gettype(0));
        $root->accept($ctx2);
        S2Logger::getLogger()->debug($ctx2->getSql(), __METHOD__);
        $this->assertEquals($sql3, $ctx2->getSql());

        $ctx3 = new CommandContextImpl();
        $ctx3->addArg("job", "CLERK", gettype(""));
        $ctx3->addArg("deptno", 20, gettype(20));
        $root->accept($ctx3);
        S2Logger::getLogger()->debug($ctx3->getSql(), __METHOD__);
        $this->assertEquals($sql4, $ctx3->getSql());

        $ctx4 = new CommandContextImpl();
        $ctx4->addArg("deptno", 20, gettype(20));
        $ctx4->addArg("job", null, gettype(""));
        $root->accept($ctx4);
        S2Logger::getLogger()->debug($ctx4->getSql(), __METHOD__);
        $this->assertEquals($sql5, $ctx4->getSql());
    }

    public function testBeginAnd() {
        $sql = "/*BEGIN*/WHERE /*IF true*/aaa BETWEEN /*bbb*/111 AND /*ccc*/123/*END*//*END*/";
        $sql2 = "WHERE aaa BETWEEN ? AND ?";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $ctx->addArg("bbb", "111", gettype(""));
        $ctx->addArg("ccc", "222", gettype(""));
        $root->accept($ctx);
        S2Logger::getLogger()->debug("[" . $ctx->getSql() . "]", __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
    }

    public function testIn() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE deptno IN /*deptnoList*/(10, 20) ORDER BY ename";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno IN (?, ?) ORDER BY ename";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $deptnoList = new \SplFixedArray(2);
        $deptnoList->offsetSet(0, 10);
        $deptnoList->offsetSet(1, 20);
        $ctx->addArg("deptnoList", $deptnoList, gettype($deptnoList->toArray()));
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars[0]));
        $this->assertEquals(10, $vars[0]);
    }

    public function testIn2() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE deptno IN /*deptnoList*/(10, 20) ORDER BY ename";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno IN (?, ?) ORDER BY ename";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $deptnoArray = \SplFixedArray::fromArray(array(10, 20));
        $ctx->addArg("deptnoList", $deptnoArray, gettype($deptnoArray));
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars[0]));
        $this->assertEquals(10, $vars[0]);
    }

    public function testIn3() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE ename IN /*enames*/('SCOTT','MARY') AND job IN /*jobs*/('ANALYST', 'FREE')";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE ename IN (?, ?) AND job IN (?, ?)";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $enames = \SplFixedArray::fromArray(array("SCOTT", "MARY"));
        $jobs = \SplFixedArray::fromArray(array("ANALYST", "FREE"));
        $ctx->addArg("enames", $enames, gettype($enames));
        $ctx->addArg("jobs", $jobs, gettype($jobs));
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars[0]));
        $this->assertEquals("SCOTT", $vars[0]);
        $this->assertEquals("MARY", $vars[1]);
    }

    public function testParseBindVariable3() {
        $sql = "BETWEEN sal ? AND ?";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $ctx->addArg('$1', 0, gettype(0));
        $ctx->addArg('$2', 1000, gettype(1000));
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals($sql, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(2, count($vars));
        $this->assertEquals(0, $vars[0]);
        $this->assertEquals(1000, $vars[1]);
    }

    /**
     * @expectedException S2Dao\Exception\EndCommentNotFoundRuntimeException
     */
    public function testEndNotFound() {
        $sql = "/*BEGIN*/";
        $parser = new SqlParserImpl($sql);
        $parser->parse();
        $this->fail("期待通りの例外が発生しませんでした。");
    }

    public function testEndParent() {
        $sql = "INSERT INTO ITEM (ID, NUM) VALUES (/*id*/1, /*num*/20)";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $ctx->addArg("id", 0, gettype(0));
        $ctx->addArg("num", 1, gettype(1));
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals(true, preg_match('/\)$/', $ctx->getSql()) == 1);
    }

    public function testEmbeddedValue() {
        $sql = '/*$aaa*/';
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $ctx->addArg("aaa", 0, gettype(0));
        $root->accept($ctx);
        S2Logger::getLogger()->debug($ctx->getSql(), __METHOD__);
        $this->assertEquals("", $ctx->getSql());
    }
}
