<?php
namespace PDOMocker;
use \PHPUnit_Framework_MockObject_Stub_Return as ReturnValue;
use \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount as Any;
use \PHPUnit_Framework_MockObject_Generator as Generator;
use \PHPUnit_Framework_MockObject_Stub_ReturnCallback as ReturnCallback;

class Mocker
{
    protected $mockGenerator = null;
    protected $queries = array();
    protected $transactionStarted = false;
    protected $lastInsertId = 0;
    
    public function __construct()
    {
        $this->mockGenerator = new Generator;
    }
    
    protected function createPdoStatement($resultSet = array())
    {
        $pdoStatement = $this->mockGenerator->getMock('PDOStatement',array ('fetchAll','fetch','rowCount','bindValue','execute'));
        
        if(count($resultSet) == 0) {
            $fetch = new ReturnValue(false);
        } else {
            $fetch = new ReturnValue($resultSet[0]);
        }
        
        $pdoStatement->expects(new Any)
                     ->method('fetch')
                     ->will($fetch);

        $pdoStatement->expects(new Any)
                     ->method('fetchAll')
                     ->will(new ReturnValue($resultSet));  

        $pdoStatement->expects(new Any)
                     ->method('rowCount')
                     ->will(new ReturnValue(count($resultSet)));
        
        $pdoStatement->expects(new Any)
                     ->method('bindValue')
                     ->will(new ReturnValue(true));
        
        $pdoStatement->expects(new Any)
                     ->method('execute')
                     ->will(new ReturnValue(true));                                                         
                     
        return $pdoStatement;                               
    }    
    
    protected function processQueryResultSet()
    {
        return function() {
            $sql = preg_replace('/\s+/', ' ', func_get_args()[0]);
            return $this->createPdoStatement(
                isset($this->queries[$sql])?
                    $this->queries[$sql]->execute():
                    array()
                );
        }; 
    }

    public function getExecutionCount($sql)
    {
        $filteredSql = preg_replace('/\s+/', ' ', $sql);
        return isset($this->queries[$filteredSql])?$this->queries[$filteredSql]->getExecutionCount():0;
    }
    
    protected function processBeginTransaction()
    {
        return function() {
            return $this->transactionStarted = true;
        };
    }
    
    protected function processRollback()
    {
        return function() {
            $transactionStarted = $this->transactionStarted;
            $this->transactionStarted = false;
            return $transactionStarted;
        };
    }    
    
    protected function processCommitAndInTransaction()
    {
        return function() {
            $transactionStarted = $this->transactionStarted;
            $this->transactionStarted = false;
            return $transactionStarted;
        };
    }    
           
    public function registerQuery(Query $query)
    {
        $this->queries[$query->getSql()] = $query;
        return $this;
    }    
    
    public function setLastInsertId($lastInsertId)
    {
        $this->lastInsertId = (int)$lastInsertId;
        return $this;
    }    
    
    public function getMock($expectedClass=null)
    {       
        $methods = array(
            'query', 
            'prepare',             
            'beginTransaction', 
            'commit', 
            'inTransaction', 
            'rollback', 
            'setAttribute',
            'lastInsertId');
        $constructor = array('sqlite::memory:');
        
        if(null !== $expectedClass) {
            $mock = $this->mockGenerator->getMock('PDO', $methods, $constructor, $expectedClass);            
        } else {
            $mock = $this->mockGenerator->getMock('PDO', $methods, $constructor);            
        }
        
        $mock->expects(new Any)
             ->method('query')
             ->will(new ReturnCallback($this->processQueryResultSet()));
        
        $mock->expects(new Any)
             ->method('prepare')
             ->will(new ReturnCallback($this->processQueryResultSet()));        
             
        $mock->expects(new Any)
             ->method('beginTransaction')
             ->will(new ReturnCallback($this->processBeginTransaction()));

        $mock->expects(new Any)
             ->method('commit')
             ->will(new ReturnCallback($this->processCommitAndInTransaction()));

        $mock->expects(new Any)
             ->method('inTransaction')
             ->will(new ReturnCallback($this->processCommitAndInTransaction()));             
        
        $mock->expects(new Any)
             ->method('rollBack')
             ->will(new ReturnCallback($this->processRollback()));     

        $mock->expects(new Any)
             ->method('setAttribute')
             ->will(new ReturnValue(true));
              
        $mock->expects(new Any)
             ->method('lastInsertId')
             ->will(new ReturnValue($this->lastInsertId));
             
        return $mock;
    }
}