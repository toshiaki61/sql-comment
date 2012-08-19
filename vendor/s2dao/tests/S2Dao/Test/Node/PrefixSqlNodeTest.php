<?php
namespace S2Dao\Test\Node;

use S2Dao\Node\PrefixSqlNode;

class PrefixSqlNodeTest extends \PHPUnit_Framework_TestCase {

    public function testPrefix() {
        $node = new PrefixSqlNode('prefix', 'sql');
        $this->assertEquals('prefix', $node->getPrefix());
        $this->assertEquals('sql', $node->getSql());
    }
}
