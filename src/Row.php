<?php
namespace PDOMocker;
class Row implements \ArrayAccess
{
    protected $row; 
    protected $visible = true;
    
    public function __construct($row, $visible=true)
    {
        $this->row = (array)$row;
        $this->visible = $visible;
    }
    
    public function isVisible()
    {
        return $this->visible;
    }
    
    public function setVisible($visible=true)
    {
        $this->visible = (bool)$visible;
        return $this;
    }
    
    public function setRow(Row $row)
    {
        $this->row = $row->getRow();
        return $this;
    }  
    
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->row[] = $value;
        } else {
            $this->row[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->row[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->row[$offset]);
    }

    public function offsetGet($offset) 
    {
        return isset($this->row[$offset]) ? $this->row[$offset] : null;
    }      
}