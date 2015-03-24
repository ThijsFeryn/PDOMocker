<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Query\Select as SelectQuery;

class QueryTest extends \PHPUnit_Framework_TestCase 
{   
    /**
     * @expectedException        PDOMocker\Exception
     * @expectedExceptionMessage Row is not an instance of PDOMocker\Row
     */     
    public function testBadConstructor()
    {
        new SelectQuery('bla',array(new \stdClass()));
    }
    
    public function testToString()
    {
        $query = new SelectQuery('bla');        
        $this->assertEquals('bla',(string)$query);
    }
}