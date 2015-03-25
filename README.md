[![Build Status](https://travis-ci.org/ThijsFeryn/PDOMocker.svg?branch=master)](https://travis-ci.org/ThijsFeryn/PDOMocker)
# PDOMocker
A PDO mocking wrapper around [PHPUnit_MockObject](https://github.com/sebastianbergmann/phpunit-mock-objects)

## Example code
```php
<?php
namespace PDOMocker;

require_once __DIR__.'/vendor/autoload.php';

$rows = [
    new Row(['id'=>1, 'name'=>'someValue'],false),
    new Row(['id'=>2, 'name'=>'someOtherValue'],false)
];

$updatedRows = [
    new Row(['id'=>1, 'name'=>'newValue'],false),
    new Row(['id'=>2, 'name'=>'otherNewValue'],false)
];

$exception = new \PDOException('someError');

$mocker = new Mocker(); 
$mocker
    ->registerQuery(new Query\Select('SELECT * FROM someTable WHERE id=1',[$rows[0]]))
    ->registerQuery(new Query\Select('SELECT * FROM someTable',$rows))    
    ->registerQuery(new Query\Insert("INSERT INTO someTable (id,name) VALUES (1,'someValue')",[$rows[0]]))
    ->registerQuery(new Query\Update('UPDATE someTable WHERE id=1',[$rows[0]],[$updatedRows[0]]))    
    ->registerQuery(new Query\Insert("INSERT INTO otherTable (id,name) VALUES (1,'someValue')",[],$exception))
    ->registerQuery(new Query\Delete('DELETE FROM someTable WHERE id=1',[$rows[0]]));        
        
$pdo = $mocker->getMock();  

$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
/**
 * array(0) {
 * }
 */
var_dump($stmt->fetchAll());

$pdo->query("INSERT INTO someTable (id,name) VALUES (1,'someValue')"); 

$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
/**
 * array(2) {
 *  [0] =>
 *  array(2) {
 *    'id' =>
 *    int(1)
 *    'name' =>
 *    string(9) "someValue"
 *  }
 *}
 */
var_dump($stmt->fetchAll());

$pdo->query('UPDATE someTable WHERE id=1');               
 
$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
/**
 * array(2) {
 *  [0] =>
 *  array(2) {
 *    'id' =>
 *    int(1)
 *    'name' =>
 *    string(9) "newValue"
 *  }
 *}
 */
var_dump($stmt->fetchAll());

$pdo->query('DELETE FROM someTable WHERE id=1');                

/**
 * array(0) {
 * }
 */
$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
var_dump($stmt->fetchAll());

/**
 * string(9) "someError"
 */
try {
    $pdo->query("INSERT INTO otherTable (id,name) VALUES (1,'someValue')");    
} catch(\PDOException $e) {
    var_dump($e->getMessage());
}
```