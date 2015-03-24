<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Row;

class RowTest extends \PHPUnit_Framework_TestCase 
{    
    public function testVisibility()
    {         
        $row = new Row(['id'=>1, 'name'=>'someValue']);
        $this->assertTrue($row->isVisible());
        $this->assertInstanceOf('PDOMocker\Row',$row->setVisible(false));
        $this->assertFalse($row->isVisible());
        $this->assertInstanceOf('PDOMocker\Row',$row->setVisible(true));        
        $this->assertTrue($row->isVisible());   
        $this->assertInstanceOf('PDOMocker\Row',$row->setVisible(false));
        $this->assertFalse($row->isVisible());
        $this->assertInstanceOf('PDOMocker\Row',$row->setVisible());        
        $this->assertTrue($row->isVisible());             
    }  
    
    public function testGetAndSetRow()
    {
        $row = new Row(['id'=>1, 'name'=>'someValue']);
        $newRow = new Row(['id'=>2, 'name'=>'someOtherValue']);        
        
        $rowArray = $row->getRow();        
        $this->assertInternalType('array',$rowArray);
        $this->assertArrayHasKey('id',$rowArray);
        $this->assertEquals(1,$rowArray['id']);
        $this->assertArrayHasKey('name',$rowArray);
        $this->assertEquals('someValue',$rowArray['name']);
        
        $this->assertInstanceOf('PDOMocker\Row',$row->setRow($newRow));
        
        $rowArray = $row->getRow();        
        $this->assertInternalType('array',$rowArray);
        $this->assertArrayHasKey('id',$rowArray);
        $this->assertEquals(2,$rowArray['id']);
        $this->assertArrayHasKey('name',$rowArray);
        $this->assertEquals('someOtherValue',$rowArray['name']);        
    }
    
    public function testEmptyConstructor()
    {
        $row = new Row();
        $this->assertInternalType('array',$row->getRow());
        $this->assertCount(0,$row->getRow());                
    }
}