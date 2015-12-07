<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Delete extends Query
{
    public function execute()
    {
        $this->executionCount++;
        if($this->exception !== null) {
            throw $this->exception;
        }        
        foreach($this->rows as $row) {
            $row->setVisible(false);               
        }
         
        return array();
    }
}