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

$mocker = new Mocker(); 
$mocker
    ->registerQuery(new Query\Select('SELECT * FROM someTable WHERE id=1',$rows))
    ->registerQuery(new Query\Insert('INSERT INTO someTable WHERE id=1',$rows))
    ->registerQuery(new Query\Update('UPDATE someTable WHERE id=1',$rows,$updatedRows))    
    ->registerQuery(new Query\Delete('DELETE FROM someTable WHERE id=1',$rows));    
        
$pdo = $mocker->getMock();  

$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
/**
 * array(0) {
 * }
 */
var_dump($stmt->fetchAll());

$pdo->query('INSERT INTO someTable WHERE id=1');                

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
 *  [1] =>
 *  array(2) {
 *    'id' =>
 *    int(2)
 *    'name' =>
 *    string(14) "someOtherValue"
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
 *  [1] =>
 *  array(2) {
 *    'id' =>
 *    int(2)
 *    'name' =>
 *    string(14) "otherNewValue"
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
```