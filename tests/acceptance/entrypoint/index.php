<?php

declare(strict_types=1);

require_once(dirname(__DIR__, 3) . '/vendor/autoload.php');

error_reporting(E_ALL);

$params = explode('/', $_SERVER['REQUEST_URI']);
unset($params[0]);
$params = array_values($params);

$testFile = $params[0];
$testMethod = $params[1];

require_once(dirname(__DIR__, 1) . '/Controller/' . $testFile . '.php');
$testFile = "Aloefflerj\YetAnotherController\\$testFile";

$test = new $testFile('');
$methodToDispatch = $test->$testMethod();
$methodToDispatch();
