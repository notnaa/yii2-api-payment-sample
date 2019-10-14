<?php
$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['components']['db']['dsn'] = 'mysql:host=localhost;dbname=api-payment-sample-tests';

return $db;
