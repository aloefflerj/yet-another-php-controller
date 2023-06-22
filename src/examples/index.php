<?php

declare(strict_types=1);

use Aloefflerj\YetAnotherController\Controller\Controller;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$controller = new Controller('http://localhost:8000');
$controller->get('/oi', function () { return; });

dd($controller);
