<?php

declare(strict_types=1);

use Aloefflerj\YetAnotherController\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$controller = new Controller('http://localhost:8000');

$mascotsFixture = file_get_contents('./mascots-fixture.json');
$mascotsFixture = json_decode($mascotsFixture);

$controller->get('/', function (ServerRequestInterface $_, ResponseInterface $response) {
    $response->getBody()->write('Welcome home');
    return $response;
});

$controller->get(
    '/mascots',
    function (
        ServerRequestInterface $_,
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
        ServerRequestInterface $_,
        ResponseInterface $response,
        \stdClass $args
    ) use ($mascotsFixture) {

        $response = $response->withHeader('Content-Type', 'application/json');

        $foundMascot = array_column($mascotsFixture, null, 'id')[$args->id] ?? new \stdClass();

        $response->getBody()->write(
            json_encode($foundMascot, JSON_PRETTY_PRINT)
        );

        return $response;
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
        ServerRequestInterface $request,
        ResponseInterface $response,
    ) use ($mascotsFixture) {
        $requestBodyContent = $request->getBody()->getContents();
        $requestBody = json_decode($requestBodyContent);

        $response = $response->withStatus(201);
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
        ServerRequestInterface $request,
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
