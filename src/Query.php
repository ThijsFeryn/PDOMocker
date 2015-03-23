<?php
namespace PDOMocker;

abstract class Query implements \ArrayAccess
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
    
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->rows[] = $value;
        } else {
            $this->rows[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->rows[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->rows[$offset]);
    }

    public function offsetGet($offset) 
    {
        return isset($this->rows[$offset]) && $this->rows[$offset]->isVisible() ? $this->rows[$offset] : null;
    }   
}