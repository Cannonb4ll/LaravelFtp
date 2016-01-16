<?php

ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use LaravelFtp\Ftp;

$ftp = new Ftp('80.82.209.146', 'Dennis.17413', 'BOzAtjDx98');

echo '<pre>';

print_r($ftp->all());