<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Row;

class RowSequenceTest extends \PHPUnit_Framework_TestCase
{
    public function testSequence()
    {
        $sequence = new Row\Sequence(
            [
                new Row(['id'=>1, 'name'=>'someValue']),
                new Row(['id'=>2, 'name'=>'otherValue'],false)
            ]
        );

        $this->assertTrue($sequence->isVisible());
        $row = $sequence->getRow();
        $this->assertInternalType('array',$row);
        $this->assertEquals(1,$row['id']);
        $this->assertEquals('someValue',$row['name']);

        $this->assertFalse($sequence->isVisible());
        $row = $sequence->getRow();
        $this->assertInternalType('array',$row);
        $this->assertEquals(2,$row['id']);
        $this->assertEquals('otherValue',$row['name']);

        $this->assertFalse($sequence->isVisible());
        $row = $sequence->getRow();
        $this->assertInternalType('array',$row);
        $this->assertEquals(2,$row['id']);
        $this->assertEquals('otherValue',$row['name']);
    }
}