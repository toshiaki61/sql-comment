<?php
namespace S2Dao\Test\Node;
use S2Dao\PHPType;

use S2Dao\Impl\SqlParserImpl;

use S2Dao\Impl\CommandContextImpl;

class EmbeddedValueNodeTest extends \PHPUnit_Framework_TestCase {
    public function testPropertyName() {
        $sql = 'SELECT * FROM EMP2 emp2 WHERE /*IF aaa.bbb != null*//*$aaa.bbb*/\'\'/*END*/';
        $parser = new SqlParserImpl($sql);
        $ctx = new CommandContextImpl;
        $entity = new M_EmbeddedValueNode();
        $entity->setBbb('111');
        $ctx->addArg('aaa', $entity, PHPType::getType($entity));
        $node = $parser->parse();
        $node->accept($ctx);
        $this->assertEquals('SELECT * FROM EMP2 emp2 WHERE 111', $ctx->getSql());
    }
}
class M_EmbeddedValueNode {
    private $bbb;
    public function getBbb() {
        return $this->bbb;
    }
    public function setBbb($bbb) {
        $this->bbb = $bbb;
    }
}
