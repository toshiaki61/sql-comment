<?php
namespace S2Dao\Test\Node;
use S2Dao\Node\ParenBindVariableNode;

use S2Dao\Node\EmbeddedValueNode;

use S2Dao\PHPType;

use S2Dao\Impl\SqlParserImpl;

use S2Dao\Impl\CommandContextImpl;

class ParenBindVariableNodeTest extends \PHPUnit_Framework_TestCase {

    public function testArray() {
        $sql = "SELECT * FROM EMP2 emp2 WHERE deptno IN /*deptnoList*/(10, 20) ORDER BY ename";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno IN (?, ?, ?) ORDER BY ename";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $deptnoList = [];
        $deptnoList[] = 10;
        $deptnoList[] = 20;
        $deptnoList[] = 30;
        $ctx->addArg("deptnoList", $deptnoList, gettype($deptnoList));
        $root->accept($ctx);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(3, count($vars));
        $this->assertEquals(10, $vars[0]);
    }

    public function testEmptyArray() {
//         $sql = "SELECT * FROM EMP2 emp2 /*BEGIN*/WHERE /*IF deptnoList != null*/deptno IN /*deptnoList*/(10, 20)/*END*/ /*END*/ORDER BY ename";
//         $sql2 = "SELECT * FROM EMP2 emp2 ORDER BY ename";
        $sql = "SELECT * FROM EMP2 emp2 WHERE deptno IN /*deptnoList*/(10, 20) ORDER BY ename";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno IN  ORDER BY ename";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $deptnoList = [];
        $ctx->addArg("deptnoList", $deptnoList, gettype($deptnoList));
        $root->accept($ctx);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(0, count($vars));
    }

    public function testNull() {
//         $sql = "SELECT * FROM EMP2 emp2 /*BEGIN*/WHERE /*IF deptnoList != null*/deptno IN /*deptnoList*/(10, 20)/*END*/ /*END*/ORDER BY ename";
//         $sql2 = "SELECT * FROM EMP2 emp2 ORDER BY ename";
        $sql = "SELECT * FROM EMP2 emp2 WHERE deptno IN /*deptnoList*/(10, 20) ORDER BY ename";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno IN  ORDER BY ename";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $ctx->addArg("deptnoList", null, gettype(null));
        $root->accept($ctx);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(0, count($vars));
    }

    public function testObject() {
        $sql = "SELECT * FROM EMP2 emp2 /*BEGIN*/WHERE /*IF deptnoList.bbb != null*/deptno IN /*deptnoList*/(10, 20)/*END*/ /*END*/ORDER BY ename";
        $sql2 = "SELECT * FROM EMP2 emp2 WHERE deptno IN ? ORDER BY ename";
        $parser = new SqlParserImpl($sql);
        $root = $parser->parse();
        $ctx = new CommandContextImpl();
        $arg = new M_ParenBindVariableNode;
        $ctx->addArg("deptnoList", $arg, PHPType::getType($arg));
        $root->accept($ctx);
        $this->assertEquals($sql2, $ctx->getSql());
        $vars = $ctx->getBindVariables();
        $this->assertEquals(1, count($vars));
    }

    public function testExpression() {
        $node = new ParenBindVariableNode('aaa');
        $this->assertEquals('aaa', $node->getExpression());
    }
}
class M_ParenBindVariableNode {
    private $bbb;
    public function getBbb() {
        return $this->bbb;
    }
    public function setBbb($bbb) {
        $this->bbb = $bbb;
    }
}
