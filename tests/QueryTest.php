<?php
namespace PDOMocker\tests;
require_once __DIR__.'/../vendor/autoload.php';
use PDOMocker\Query\Select as SelectQuery;
use PDOMocker\Row as Row;

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
    
    /**
     * @expectedException        PDOException
     * @expectedExceptionMessage someError
     */     
    public function testThrowException()
    {
        $query = new SelectQuery('bla',array(),new \PDOException('someError'));
        $query->execute();
    }

    public function testSelectQueryWithRows()
    {
        $query = new SelectQuery('bla',[new Row(['key'=>'value']), new Row(['key'=>'otherValue'])]);
        $rows = $query->execute();
        $this->assertEquals('value',$rows[0]['key']);
        $this->assertEquals('otherValue',$rows[1]['key']);
    }

    public function testSelectQueryWithRowSequence()
    {
        $query = new SelectQuery(
            'bla',
            [
                new Row\Sequence(
                    [
                        new Row(
                            [
                                'key'=>'value'
                            ]
                        ),
                        new Row(
                            [
                                'key'=>'otherValue'
                            ]
                        )
                    ]
                ),
                new Row\Sequence(
                    [
                        new Row(
                            [
                                'key'=>'yetAnotherValue',
                            ]
                        ),
                        new Row(
                            [
                                'key'=>'andYetAnotherValue'
                            ],
                            false
                        )
                    ]
                )
            ]
        );

        $rows = $query->execute();
        $this->assertEquals('value',$rows[0]['key']);
        $this->assertEquals('yetAnotherValue',$rows[1]['key']);

        $rows = $query->execute();
        $this->assertEquals('otherValue',$rows[0]['key']);
        $this->assertArrayNotHasKey(1,$rows);
    }

    public function testExecutionCount()
    {
        $query = new SelectQuery('bla');
        $this->assertEquals(0,$query->getExecutionCount());
        $query->execute();
        $this->assertEquals(1,$query->getExecutionCount());
        $query->execute();
        $this->assertEquals(2,$query->getExecutionCount());
    }
}

