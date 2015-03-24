<?php
namespace PDOMocker;
class Row
{
    protected $row; 
    protected $visible = true;
    
    public function __construct($row=array(), $visible=true)
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
    
    public function getRow()
    {
        return $this->row;
    }
}