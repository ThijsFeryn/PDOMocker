<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Delete extends Query
{
    public function execute()
    {
         foreach($this->rows as $row) {
             $row->setVisible(false);               
         }
         
         return array();
    }
}