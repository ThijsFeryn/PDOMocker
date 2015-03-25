<?php
namespace PDOMocker;

abstract class Query
{
    protected $sql;
    protected $rows = array(); 
    protected $exception;
    
    public function __construct($sql, $rows=array(), \Exception $exception=null)
    {
        $this->sql = preg_replace('/\s+/', ' ', $sql);
        foreach($rows as $row) {
            if(!$row instanceof Row) {
                throw new Exception("Row is not an instance of PDOMocker\Row");                
            }
        }
        $this->rows = $rows;              
        if($exception !== null) {
            $this->exception = $exception;
        }
    }
       
    public function getSql()
    {
        return $this->sql;
    }
        
    abstract public function execute();
  
    public function __toString()
    {
        return $this->sql;
    }    
}