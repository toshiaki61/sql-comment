<?php
namespace S2Dao\Test\Node;
use S2Dao\Node\BindVariableNode;

use S2Dao\PHPType;

use S2Dao\Impl\SqlParserImpl;

use S2Dao\Impl\CommandContextImpl;

class BindVariableNodeTest extends \PHPUnit_Framework_TestCase {

    public function testPropertyName() {
        $sql = 'SELECT * FROM EMP2 emp2 WHERE /*IF aaa.bbb != null*//*aaa.bbb*/\'\'/*END*/';
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl;
        $entity = new M_BindVariableNode();
        $entity->setBbb('111');
        $ctx->addArg('aaa', $entity, PHPType::getType($entity));
        $node = $parser->parse();
        $node->accept($ctx);
        $this->assertEquals('SELECT * FROM EMP2 emp2 WHERE ?', $ctx->getSql());
    }

    public function testPropertyNameArgIsNull() {
        $ctx = new CommandContextImpl;
        $ctx->addArg('aaa', null, PHPType::getType(null));
        $node = new BindVariableNode('aaa.bbb');
        $node->accept($ctx);
        $this->assertEquals('?', $ctx->getSql());
    }

    public function testPropertyNameArgIsNotObject() {
        $ctx = new CommandContextImpl;
        $ctx->addArg('aaa', 1, PHPType::getType(1));
        $node = new BindVariableNode('aaa.bbb');
        $node->accept($ctx);
        $this->assertEquals('?', $ctx->getSql());
    }
}
class M_BindVariableNode {
    private $bbb;
    public function getBbb() {
        return $this->bbb;
    }
    public function setBbb($bbb) {
        $this->bbb = $bbb;
    }
}
