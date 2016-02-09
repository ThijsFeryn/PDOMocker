<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Select extends Query
{
    public function execute()
    {
        $this->sharedExecution();
        
        $rows = array();      
        foreach($this->rows as $row) {
            if($row->isVisible()) {
                $rows[] = $row->getRow();    
            }
        } 
        return $rows;
    }
}