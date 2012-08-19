<?php
namespace SqlComment\Test;
use SqlComment\SqlComment;
class SqlCommentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \SqlComment\SqlComment
     */
    protected $sql;
    public function testSql()
    {
        $this->sql = new SqlComment(
                file_get_contents(__DIR__ . '/../../resources/testVariable.sql'));
        $this->assertContains('WHERE ? AND', $this->sql->parse(array(1, 333), array('test', 'aaa')));
    }

    public function testSubstr()
    {
        $this->assertEquals('e', substr('abcde', -1, 1));
    }
}
