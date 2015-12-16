<?php
namespace PDOMocker\Query;
use PDOMocker\Query;
use PDOMocker\Row;

class Update extends Query
{
    protected $updatedRows = array();
    
    public function __construct($sql, $rows=array(), $updatedRows=array(), \Exception $exception=null)
    {
        parent::__construct($sql, $rows, $exception);
        if(count($rows) != count($updatedRows) && count($updatedRows) > 0) {
            throw new Exception("Row count for rows and updated rows should be the same");             
        }
        foreach($updatedRows as $row) {
            if(!$row instanceof Row) {
                throw new Exception("Row is not an instance of PDOMocker\\Row");
            }
        }
        $this->updatedRows = $updatedRows;
    }
    
    public function execute()
    {
        if($this->exception !== null) {
            throw $this->exception;
        }        
         
        foreach($this->rows as $index=>$row) {
            $row->setRow($this->updatedRows[$index]);               
        }
         
        return $this->rows;
    }
}