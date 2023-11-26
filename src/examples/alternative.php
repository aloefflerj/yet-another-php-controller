<?php

declare(strict_types=1);

use Aloefflerj\YetAnotherController\Controller\Weird\WeirdController;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$controller = new WeirdController('http://localhost:8000');
$controller->route(
    '/mascots'
)->get(
    function () {
        echo 'mascots';
    },
    function (int $id) {
        echo 'mascot ' . $id;
    },
)->post(
    function (\stdClass $body) {
        echo "mascot name: {$body->mascot}";
    }
)->put(
    function (int $id, \stdClass $body) {
        echo "mascot with id {$id} is: {$body->mascot}";
    }
);

$controller->route(
    '/new'
)->get(
    function () {
        echo 'new';
    }
)->dispatch();