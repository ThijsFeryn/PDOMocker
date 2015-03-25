<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Insert extends Query
{
    public function execute()
    {
        if($this->exception !== null) {
            throw $this->exception;
        }
                
        foreach($this->rows as $row) {
            $row->setVisible(true);             
        }
         
        return $this->rows;
    }
}