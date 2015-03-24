<?php
namespace PDOMocker;

abstract class Query
{
    protected $sql;
    protected $rows = array(); 
    
    public function __construct($sql, $rows=array())
    {
        $this->sql = preg_replace('/\s+/', ' ', $sql);
        foreach($rows as $row) {
            if(!$row instanceof Row) {
                throw new Exception("Row is not an instance of PDOMocker\Row");                
            }
        }
        $this->rows = $rows;              
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