<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Delete extends Query
{
    public function execute()
    {
        $this->sharedExecution();
        foreach($this->rows as $row) {
            $row->setVisible(false);               
        }
         
        return array();
    }
}