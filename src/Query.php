<?php
namespace PDOMocker;

use PDOMocker\Row\RowInterface;

abstract class Query
{
    protected $sql;
    protected $rows = array(); 
    protected $exception;
    protected $executed = false;
    protected $throwExceptionOnSecondExecution=false;
    
    public function __construct($sql, $rows=array(), \Exception $exception=null,$throwExceptionOnSecondExecution=false)
    {
        $this->sql = preg_replace('/\s+/', ' ', $sql);
        foreach($rows as $row) {
            if(!$row instanceof RowInterface) {
                throw new Exception("Row is not an instance of PDOMocker\\Row\\RowInterface");
            }
        }
        $this->rows = $rows;              
        if($exception !== null) {
            $this->exception = $exception;
        }
        $this->throwExceptionOnSecondExecution = $throwExceptionOnSecondExecution;
    }
       
    public function getSql()
    {
        return $this->sql;
    }
        
    abstract public function execute();
  
    public function __toString()
    {
        return $this->sql;
    }

    protected function sharedExecution()
    {
        if($this->exception !== null && !$this->throwExceptionOnSecondExecution) {
            throw $this->exception;
        }

        if($this->throwExceptionOnSecondExecution && $this->executed) {
            if($this->exception !== null) {
                throw $this->exception;
            } else {
                throw new Exception;
            }
        }
        $this->executed = true;
    }
}