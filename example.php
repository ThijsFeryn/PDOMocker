<?php
namespace PDOMocker;

require_once __DIR__.'/vendor/autoload.php';

$rows = [
    new Row(['id'=>1, 'name'=>'someValue'],false),
    new Row(['id'=>2, 'name'=>'someOtherValue'],false)
];

$mocker = new Mocker(); 
$mocker
    ->registerQuery(new Query\Select('SELECT * FROM someTable WHERE id=1',$rows))
    ->registerQuery(new Query\Insert('INSERT INTO someTable WHERE id=1',$rows));
        
$pdo = $mocker->getMock();                  
$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
echo $stmt->fetchAll()[1]['name']; //someValue