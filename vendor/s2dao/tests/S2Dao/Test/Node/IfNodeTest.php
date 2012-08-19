<?php
namespace S2Dao\Test\Node;
use S2Dao\Impl\CommandContextImpl;

use S2Dao\Node\IfNode;

class IfNodeTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \S2Dao\Exception\IllegalBoolExpressionRuntimeException
     */
    public function testIllegalExpression() {
        $node = new IfNode('--test');

        $ctx = new CommandContextImpl();
        $node->accept($ctx);
        $this->fail("期待通りの例外が発生しませんでした。");
    }

    public function testIllegalExpressionEx() {
        try {
            $node = new IfNode('--test');

            $ctx = new CommandContextImpl();
            $node->accept($ctx);
        } catch (\S2Dao\Exception\IllegalBoolExpressionRuntimeException $e) {
            $this->assertEquals('--test', $e->getExpression());
        }
    }

    /**
     * @expectedException \S2Dao\Exception\IllegalBoolExpressionRuntimeException
     */
    public function testIllegalExpressionIsNull() {
        $node = new IfNode('null');

        $ctx = new CommandContextImpl();
        $node->accept($ctx);
        $this->fail("期待通りの例外が発生しませんでした。");
    }

    public function testValueIsNullAndTypeIsObject() {
        $node = new IfNode('aaa.bbb != null');

        $ctx = new CommandContextImpl();
        $arg = new \stdClass();
        $ctx->addArg('aaa', null, gettype($arg));
        $node->accept($ctx);
        $this->assertTrue(true);
    }

    public function testValueIsStdClassAndTypeIsObject() {
        $node = new IfNode('aaa.bbb != null');

        $ctx = new CommandContextImpl();
        $arg = new \stdClass();
        $arg->bbb = 111;
        $ctx->addArg('aaa', $arg, gettype($arg));
        $node->accept($ctx);
        $this->assertTrue(true);
    }

    public function testValueIsStringAndTypeIsObject() {
        $node = new IfNode('aaa.bbb != null');

        $ctx = new CommandContextImpl();
        $arg = new \stdClass();
        $ctx->addArg('aaa', 'test', gettype($arg));
        $node->accept($ctx);
        $this->assertTrue(true);
    }
}
