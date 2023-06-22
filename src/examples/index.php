<?php

declare(strict_types=1);

use Aloefflerj\YetAnotherController\Controller\Controller;
use Psr\Http\Message\RequestInterface;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$controller = new Controller('http://localhost:8000');
$controller->get('/', function (RequestInterface $request) { echo '<pre>', var_dump($request->getBody()->getContents()), '</pre>'; });
$controller->post('/', function (RequestInterface $request) { echo ($request->getBody()->getContents()); });

$controller->dispatch();