<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Mocker;
use PDOMocker\Row;
use PDOMocker\Row\Sequence;
use PDOMocker\Query\Select as SelectQuery;
use PDOMocker\Query\Insert as InsertQuery;
use PDOMocker\Query\Delete as DeleteQuery;
use PDOMocker\Query\Update as UpdateQuery;

class PDOMockerTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @var \PDOMocker\Mocker
     */
    protected $pdoMocker;
    protected function setUp()
    {         
        $rowSomeValue = new Row(['id'=>1, 'name'=>'someValue']);
        $rowSomeOtherValue = new Row(['id'=>2, 'name'=>'someOtherValue']);

        $sequence = new Sequence([clone $rowSomeValue, clone $rowSomeOtherValue]);

        $rowTestInsert = new Row(['id'=>3, 'name'=>'yetAnotherValue'],false);
        $rowTestUpdate = new Row(['id'=>1, 'name'=>'newValue']);

        $exception = new \PDOException('someError',1);
                                        
        $this->pdoMocker = new Mocker(); 
        $this->pdoMocker->registerQuery(new SelectQuery('SELECT * FROM someTable WHERE id=1',[$rowSomeValue]))
            ->registerQuery(new SelectQuery('SELECT * FROM someTable WHERE id=2',[$rowSomeOtherValue]))            
            ->registerQuery(new SelectQuery('SELECT * FROM someTable',[$rowSomeValue, $rowSomeOtherValue]))
            ->registerQuery(new SelectQuery('SELECT * FROM someOtherTable WHERE id=3',[$rowTestInsert]))
            ->registerQuery(new SelectQuery('SELECT * FROM someKindOfTable WHERE id=1',[$sequence]))
            ->registerQuery(new InsertQuery("INSERT INTO someOtherTable (id,name) VALUES(3,'yetAnotherValue')",[$rowTestInsert]))
            ->registerQuery(new InsertQuery("INSERT INTO someOtherTable (id,name) VALUES(:id,:name)",[$rowTestInsert]))                
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

    public function testCustomMockClassUsingNamespaces()
    {
        $this->assertInstanceOf('Bla\Bla', $this->pdoMocker->getMock('Bla\Bla'));
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

    public function testSelectSequence()
    {
        $pdo = $this->pdoMocker->getMock();
        $stmt = $pdo->query('SELECT * FROM someKindOfTable WHERE id=1');
        $fetch = $stmt->fetch();
        $this->assertEquals('someValue',$fetch['name']);
        $stmt = $pdo->query('SELECT * FROM someKindOfTable WHERE id=1');
        $fetch = $stmt->fetch();
        $this->assertEquals('someOtherValue',$fetch['name']);
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
    
    public function testInsertPrepared()
    {
        $pdo = $this->pdoMocker->getMock();                  
        $stmt = $pdo->query("SELECT * FROM someOtherTable WHERE id=3");  
        $this->assertInstanceOf('PDOStatement', $stmt);   
        $this->assertEquals(0,$stmt->rowCount());      
        
        $stmt = $pdo->prepare("INSERT INTO someOtherTable (id,name) VALUES(:id,:name)");  
        $stmt->bindValue(':id',3,\PDO::PARAM_INT);
        $stmt->bindValue(':name','yetAnotherValue',\PDO::PARAM_STR);
        $stmt->execute();        

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

    public function testExecutionCount()
    {
        $sql = 'SELECT * FROM someTable WHERE id=1';
        $pdo = $this->pdoMocker->getMock();

        $this->assertEquals(0,$this->pdoMocker->getExecutionCount('bla'));
        $this->assertEquals(0,$this->pdoMocker->getExecutionCount($sql));
        $pdo->query($sql);
        $this->assertEquals(1,$this->pdoMocker->getExecutionCount($sql));
        $pdo->query($sql);
        $this->assertEquals(2,$this->pdoMocker->getExecutionCount($sql));
    }

    public function testGetExecutedSqlStatements()
    {
        $sql1 = 'SELECT * FROM someTable WHERE id=1';
        $sql2 = 'SELECT * FROM someTable WHERE id=2';
        $pdo = $this->pdoMocker->getMock();
        $pdo->query($sql1);
        $pdo->query($sql1);
        $pdo->query($sql2);
        $pdo->query($sql1);
        $statements = $this->pdoMocker->getExecutedSqlStatements();
        $this->assertInternalType('array',$statements);
        $this->assertEquals($sql1,$statements[0]);
        $this->assertEquals($sql2,$statements[1]);
    }
}