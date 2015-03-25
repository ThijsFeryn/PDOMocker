<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Mocker;
use PDOMocker\Row;
use PDOMocker\Query\Select as SelectQuery;
use PDOMocker\Query\Insert as InsertQuery;
use PDOMocker\Query\Delete as DeleteQuery;
use PDOMocker\Query\Update as UpdateQuery;

class PDOMockerTest extends \PHPUnit_Framework_TestCase 
{    
    protected function setUp()
    {         
        $rowSomeValue = new Row(['id'=>1, 'name'=>'someValue']);
        $rowSomeOtherValue = new Row(['id'=>2, 'name'=>'someOtherValue']);
        
        $rowTestInsert = new Row(['id'=>3, 'name'=>'yetAnotherValue'],false);
        $rowTestUpdate = new Row(['id'=>1, 'name'=>'newValue']);        
        
        $exception = new \PDOException('someError',1);
                                        
        $this->pdoMocker = new Mocker(); 
        $this->pdoMocker
            ->registerQuery(new SelectQuery('SELECT * FROM someTable WHERE id=1',[$rowSomeValue]))
            ->registerQuery(new SelectQuery('SELECT * FROM someTable WHERE id=2',[$rowSomeOtherValue]))            
            ->registerQuery(new SelectQuery('SELECT * FROM someTable',[$rowSomeValue, $rowSomeOtherValue]))
            ->registerQuery(new SelectQuery('SELECT * FROM someOtherTable WHERE id=3',[$rowTestInsert]))
            ->registerQuery(new InsertQuery("INSERT INTO someOtherTable (id,name) VALUES(3,'yetAnotherValue')",[$rowTestInsert]))
            ->registerQuery(new DeleteQuery('DELETE FROM someTable WHERE id=1',[$rowSomeValue]))                                                            
            ->registerQuery(new UpdateQuery("UPDATE someTable SET name='newValue' WHERE id=1",[$rowSomeValue],[$rowTestUpdate]))
            ->registerQuery(new InsertQuery("INSERT INTO bla (id,name) VALUES(1,'someValue')",[],$exception));                                                                        
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
      
        $fetchAll = $stmt->fetchAll();
        $this->assertInternalType('array',$fetchAll);
        $this->assertCount(2,$fetchAll);
        $this->assertEquals(1,$fetchAll[0]['id']);
        $this->assertEquals('someValue',$fetchAll[0]['name']);
        $this->assertEquals(2,$fetchAll[1]['id']);
        $this->assertEquals('someOtherValue',$fetchAll[1]['name']);        
    } 
    
    public function testInsert()
    {
        $pdo = $this->pdoMocker->getMock();                  
        $stmt = $pdo->query("SELECT * FROM someOtherTable WHERE id=3");  
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(0,$stmt->rowCount());      
        
        $stmt = $pdo->query("INSERT INTO someOtherTable (id,name) VALUES(3,'yetAnotherValue')");  

        $stmt = $pdo->query("SELECT * FROM someOtherTable WHERE id=3");  
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(1,$stmt->rowCount());      
        
        $fetch = $stmt->fetch();
        $this->assertInternalType('array',$fetch);
        $this->assertEquals(3,$fetch['id']);
        $this->assertEquals('yetAnotherValue',$fetch['name']);                 
    }  
    
    public function testDelete()
    {
        $pdo = $this->pdoMocker->getMock();                  
        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(1,$stmt->rowCount());
        
        $stmt = $pdo->query("DELETE FROM someTable WHERE id=1");

        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(0,$stmt->rowCount());
        
        $stmt = $pdo->query("SELECT * FROM someTable");
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(1,$stmt->rowCount());                     
    } 
    
    public function testUpdate()
    {
        $pdo = $this->pdoMocker->getMock();                  
        
        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");        
        $fetch = $stmt->fetch();
        $this->assertEquals(1,$fetch['id']);
        $this->assertEquals('someValue',$fetch['name']);
        
        $stmt = $pdo->query("UPDATE someTable SET name='newValue' WHERE id=1");

        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");        
        $fetch = $stmt->fetch();
        $this->assertEquals(1,$fetch['id']);
        $this->assertEquals('newValue',$fetch['name']);       
    }    
    
    public function testQueryThatIsNotRegistered()
    {
        $pdo = $this->pdoMocker->getMock();                      
        $stmt = $pdo->query("SELECT * FROM someTable WHERE id=500");        
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(0,$stmt->rowCount());     
    }
    
    public function testTransaction()
    {
        $pdo = $this->pdoMocker->getMock();
        
        $this->assertFalse($pdo->commit());
        $this->assertTrue($pdo->beginTransaction());
        $this->assertTrue($pdo->commit());
        $this->assertFalse($pdo->commit());
        
        $this->assertTrue($pdo->beginTransaction());
        $this->assertTrue($pdo->rollback());
        $this->assertFalse($pdo->rollback());                                
        $this->assertFalse($pdo->commit());        
    }
    /**
     * @expectedException        PDOException
     * @expectedExceptionMessage someError
     */    
    public function testThrowException()
    {
        $pdo = $this->pdoMocker->getMock();        
        $pdo->query("INSERT INTO bla (id,name) VALUES(1,'someValue')");
    }
    
    public function testLastInsertId()
    {
        $pdo = $this->pdoMocker->getMock();
        $this->assertEquals(0,$pdo->lastInsertId());
        $this->pdoMocker->setLastInsertId(1);
        $pdo = $this->pdoMocker->getMock();        
        $this->assertEquals(1,$pdo->lastInsertId());        
    }
}