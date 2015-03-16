[![Build Status](https://travis-ci.org/ThijsFeryn/PDOMocker.svg?branch=master)](https://travis-ci.org/ThijsFeryn/PDOMocker)
# PDOMocker
A PDO mocking framework for PHPUnit that wraps [PHPUnit_MockObject](https://github.com/sebastianbergmann/phpunit-mock-objects)

## Example code
```php
<?php
require_once __DIR__.'/vendor/autoload.php';

$mocker = new PDOMocker\Mocker(); 
$mocker->registerQuery("SELECT * FROM someTable WHERE id=1",[['id'=>1, 'name'=>'someValue']]);
        
$pdo = $mocker->getMock();                  
$stmt = $pdo->query("SELECT * FROM someTable WHERE id=1");
echo $stmt->fetch()['name']; //someValue
```