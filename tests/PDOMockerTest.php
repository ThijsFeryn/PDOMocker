<?php
namespace PDOMocker\tests;
use PDOMocker\Mocker;
class PDOMockerTest extends \PHPUnit_Framework_TestCase 
{    
    protected function setUp()
    {                                 
        $this->pdoMocker = new Mocker(); 
        $this->pdoMocker
             ->registerQuery(
                 "SELECT * FROM someTable WHERE id=1",
                 [['id'=>1, 'name'=>'someValue']])
             ->registerQuery(
                 "SELECT * FROM someTable WHERE id=2",
                 [['id'=>2, 'name'=>'someOtherValue']])                 
             ->registerQuery(
                 "SELECT * FROM someTable",
                 [
                     ['id'=>1, 'name'=>'someValue'],
                     ['id'=>2, 'name'=>'someOtherValue']
                 ]);
    }
    
    protected function tearDown()
    {
        unset($this->pdoMocker);                
    }
    
    public function testSelectedClass()
    {
        $this->assertInstanceOf('Bla', $this->pdoMocker->getMock('Bla'));           
    }
            
    public function testSelectId1()
    {          
        $pdo = $this->pdoMocker->getMock();                  
        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(1,$stmt->rowCount());
        
        $fetch = $stmt->fetch();
        $this->assertInternalType('array',$fetch);
        $this->assertEquals(1,$fetch['id']);
        $this->assertEquals('someValue',$fetch['name']); 

        $fetchAll = $stmt->fetchAll();
        $this->assertInternalType('array',$fetchAll);
        $this->assertCount(1,$fetchAll);
        $this->assertEquals(1,$fetchAll[0]['id']);
        $this->assertEquals('someValue',$fetchAll[0]['name']);        
    }
    
    public function testSelectId2()
    {          
        $pdo = $this->pdoMocker->getMock();                  
        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=2");
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(1,$stmt->rowCount());
        
        $fetch = $stmt->fetch();
        $this->assertInternalType('array',$fetch);
        $this->assertEquals(2,$fetch['id']);
        $this->assertEquals('someOtherValue',$fetch['name']); 

        $fetchAll = $stmt->fetchAll();
        $this->assertInternalType('array',$fetchAll);
        $this->assertCount(1,$fetchAll);
        $this->assertEquals(2,$fetchAll[0]['id']);
        $this->assertEquals('someOtherValue',$fetchAll[0]['name']);        
    }
    
    public function testSelectIdAll()
    {          
        $pdo = $this->pdoMocker->getMock();                  
        $stmt = $pdo->query("SELECT * FROM someTable");
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(2,$stmt->rowCount());
        
        $fetch = $stmt->fetch();
        $this->assertInternalType('array',$fetch);
        $this->assertEquals(1,$fetch['id']);
        $this->assertEquals('someValue',$fetch['name']); 
        
        $fetch = $stmt->fetch();
        $this->assertInternalType('array',$fetch);
        $this->assertEquals(2,$fetch['id']);
        $this->assertEquals('someOtherValue',$fetch['name']);        

        $fetchAll = $stmt->fetchAll();
        $this->assertInternalType('array',$fetchAll);
        $this->assertCount(2,$fetchAll);
        $this->assertEquals(1,$fetchAll[0]['id']);
        $this->assertEquals('someValue',$fetchAll[1]['name']);
        $this->assertEquals(2,$fetchAll[0]['id']);
        $this->assertEquals('someOtherValue',$fetchAll[1]['name']);        
    }        
}