<?php
namespace PDOMocker\Query;
use PDOMocker\Query;

class Select extends Query
{
    public function execute()
    {
         return $this;
    }
}