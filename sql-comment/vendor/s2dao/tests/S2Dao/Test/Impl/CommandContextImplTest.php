<?php
namespace S2Dao\Test\Impl;

use S2Dao\Impl\CommandContextImpl;

class CommandContextImplTest extends \PHPUnit_Framework_TestCase {

    public function testObject() {
        $cc = new CommandContextImpl();
        $cc->addArg('test', 1, 'integer');
        $this->assertEquals(1, $cc->getArg('dummy'));
        $this->assertEquals('integer', $cc->getArgType('dummy'));
    }
}
