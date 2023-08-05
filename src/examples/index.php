<?php

declare(strict_types=1);

use Aloefflerj\YetAnotherController\Controller\Controller;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new Controller('http://localhost:8000');

$mascotsFixture = file_get_contents('./mascots-fixture.json');
$mascotsFixture = json_decode($mascotsFixture);

$controller->get('/', function (RequestInterface $_, ResponseInterface $response) {
    $response->getBody()->write('Welcome home');
    return $response;
});

$controller->get(
    '/mascots',
    function (
        RequestInterface $_,
        ResponseInterface $response,
    ) use ($mascotsFixture) {
        $response->getBody()->write(
            json_encode($mascotsFixture, JSON_PRETTY_PRINT)
        );
        return $response;
    },
);

$controller->get(
    '/mascots/{id}',
    function (
        RequestInterface $_,
        ResponseInterface $response,
        \stdClass $args
    ) use ($mascotsFixture) {

        $foundMascot = array_column($mascotsFixture, null, 'id')[$args->id] ?? new \stdClass();

        $response->getBody()->write(
            json_encode($foundMascot, JSON_PRETTY_PRINT)
        );

        return $response->getBody();
    },
);

/**
 * body tip (json):
 * {
 *  "id": 4,
 *	"lang": "java",
 *	"mascot": "duke"
 * }
 */
$controller->post(
    '/mascots',
    function (
        RequestInterface $request,
        ResponseInterface $response,
    ) use ($mascotsFixture) {
        $requestBodyContent = $request->getBody()->getContents();
        $requestBody = json_decode($requestBodyContent);

        $response->getBody()->write(
            json_encode([...$mascotsFixture, $requestBody], JSON_PRETTY_PRINT)
        );
        return $response;
    },
);

/**
 * body tip (json):
 * {
 *  "id": 1,
 *	"lang": "php",
 *	"mascot": "elePHPant"
 * }
 */
$controller->put(
    '/mascots/{id}',
    function (
        RequestInterface $request,
        ResponseInterface $response,
        \stdClass $args
    ) use ($mascotsFixture) {
        $requestBodyContent = $request->getBody()->getContents();
        $requestBody = json_decode($requestBodyContent);

        foreach ($mascotsFixture as $i => $mascot) {
            if ($mascot->id === (int)$args->id) {
                $mascotsFixture[$i] = $requestBody;
            }
        }

        $response->getBody()->write(
            json_encode($mascotsFixture, JSON_PRETTY_PRINT)
        );
        return $response;
    },
);


$controller->dispatch();
