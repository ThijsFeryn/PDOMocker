<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Insert extends Query
{
    public function execute()
    {
        foreach($this->rows as $row) {
            $row->setVisible(true);             
        }
         
        return $this;
    }
}