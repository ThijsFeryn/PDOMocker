<?php
namespace PDOMocker\Row;
use PDOMocker\Exception;
use PDOMocker\Row;

class Sequence implements RowInterface
{
    /**
     * @var Row[]
     */
    protected $rows;
    /**
     * @var int
     */
    protected $index=0;
    
    public function __construct($rows = array())
    {
        $this->setRows($rows);
    }
    
    public function isVisible()
    {
        return $this->rows[$this->index]->isVisible();
    }
    
    public function setVisible($visible=true)
    {
        $this->rows[$this->index]->setVisible($visible);
        return $this;
    }
    
    public function setRows($rows = array())
    {
        foreach($rows as $row) {
            if(!$row instanceof Row) {
                throw new Exception("Row should be an instance of PDOMocker\\Row");
            }
        }
        $this->rows = $rows;
        return $this;
    }

    public function getRow()
    {
        $row = $this->rows[$this->index]->getRow();
        if(isset($this->rows[$this->index+1])) {
            $this->index++;
        }
        return $row;
    }
}