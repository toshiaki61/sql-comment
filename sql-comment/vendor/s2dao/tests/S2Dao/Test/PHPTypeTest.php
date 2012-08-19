<?php
namespace S2Dao\Test;
use S2Dao\PHPType;

class PHPTypeTest extends \PHPUnit_Framework_TestCase {

    public function testObject() {
        $this->assertEquals('S2Dao\\Test\\PHPTypeTest', PHPType::getType($this));
    }

    public function testReflector() {
        $this->assertEquals('ReflectionMethod', PHPType::getType(new \ReflectionMethod($this, 'testReflector'), 1));
    }

    public function testReflectionParameter() {
        $r = new \ReflectionMethod($this, 'privateMethod');
        $p = $r->getParameters();
        $this->assertEquals('Reflector', PHPType::getType($p[0], 1));
        $this->assertEquals('integer', PHPType::getType($p[1], 1));
    }

    private function privateMethod(\Reflector $r, array $array) {

    }
}
