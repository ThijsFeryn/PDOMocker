<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Query\Update as UpdateQuery;
use PDOMocker\Row;

class UpdateQueryTest extends \PHPUnit_Framework_TestCase 
{   
    /**
     * @expectedException        PDOMocker\Exception
     * @expectedExceptionMessage Row count for rows and updated rows should be the same
     */     
    public function testBadConstructorWithBadArgumentCount()
    {        
        new UpdateQuery('bla',array(new Row()),array(new \stdClass(),new \stdClass()));
    }
    /**
     * @expectedException        PDOMocker\Exception
     * @expectedExceptionMessage Row is not an instance of PDOMocker\Row
     */     
    public function testBadConstructorWithInvalidRows()
    {        
        new UpdateQuery('bla',array(new \stdClass()),array(new Row()));
    }    
    /**
     * @expectedException        PDOMocker\Exception
     * @expectedExceptionMessage Row is not an instance of PDOMocker\Row
     */     
    public function testBadConstructorWithInvalidUpdateRows()
    {        
        new UpdateQuery('bla',array(new Row()),array(new \stdClass()));
    }    
}