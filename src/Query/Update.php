<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Update extends Query
{
    protected $updatedRows = array();
    
    public function __construct($sql, $rows=array(), $updatedRows=array())
    {
        parent::__construct($sql,$rows);
        if(count($rows) != count($updatedRows) && count($updatedRows) > 0) {
            throw new Exception("Row count for rows and updated rows should be the same");             
        }
        foreach($updatedRows as $row) {
            if(!$row instanceof Row) {
                throw new Exception("Row is not an instance of PDOMocker\Row");                
            }
        }
        $this->updatedRows = $updatedRows;
    }
    
    public function execute()
    {
         foreach($this->rows as $index=>$row) {
             $row->setRow($this->updatedRows[$index]);               
         }
         
         return $this;
    }
}